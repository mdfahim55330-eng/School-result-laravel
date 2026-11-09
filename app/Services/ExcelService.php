<?php

namespace App\Services;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    private const BN_DIGITS = ['০'=>'0','১'=>'1','২'=>'2','৩'=>'3','৪'=>'4','৫'=>'5','৬'=>'6','৭'=>'7','৮'=>'8','৯'=>'9'];

    public static function bnToAsciiDigits($value): string
    {
        return strtr((string)$value, self::BN_DIGITS);
    }

    public static function normalizeKey($value): string
    {
        return preg_replace('/[-_\s]+/u', '', mb_strtolower(trim((string)$value)));
    }

    private static function cellValue($cell)
    {
        $value = $cell->getValue();
        if ($value === null) return '';
        if (is_string($value) && str_starts_with($value, '=')) {
            try { $value = $cell->getCalculatedValue(); } catch (\Throwable $e) { return ''; }
        }
        if (is_object($value) && method_exists($value, 'getPlainText')) return trim($value->getPlainText());
        if ($value instanceof \DateTimeInterface) return $value->format('Y-m-d');
        if (is_bool($value)) return $value ? 'TRUE' : 'FALSE';
        if (is_string($value)) return trim($value);
        if (is_numeric($value) && ExcelDate::isDateTime($cell)) {
            try { return ExcelDate::excelToDateTimeObject((float)$value)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        return $value;
    }

    public static function readSheet($file, int $maxRows = 3000): array
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'rb');
        $signature = $handle ? fread($handle, 4) : '';
        if ($handle) fclose($handle);
        if (strlen($signature) < 4) throw new InvalidArgumentException('The uploaded file is empty.');
        if (substr($signature, 0, 2) === "\xD0\xCF") throw new InvalidArgumentException('This is an old .xls file. Please save it as Excel Workbook (.xlsx).');
        if (substr($signature, 0, 2) !== 'PK') throw new InvalidArgumentException('Please upload an Excel (.xlsx) file.');

        try { $book = IOFactory::load($path); }
        catch (\Throwable $e) { throw new InvalidArgumentException('Could not read this Excel file. Is it a valid .xlsx file?'); }
        $sheet = null;
        foreach ($book->getWorksheetIterator() as $candidate) {
            if ($candidate->getHighestRow() > 0) { $sheet = $candidate; break; }
        }
        if (!$sheet) throw new InvalidArgumentException('No worksheet found in the Excel file.');

        $headerRow = 0;
        $maxCol = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
        for ($r = 1; $r <= min($sheet->getHighestRow(), 20); $r++) {
            $has = false;
            for ($col = 1; $col <= $maxCol; $col++) {
                if (self::cellValue($sheet->getCell(Coordinate::stringFromColumnIndex($col).$r)) !== '') { $has = true; break; }
            }
            if ($has) { $headerRow = $r; break; }
        }
        if (!$headerRow) throw new InvalidArgumentException('Excel file is empty.');

        $headers = []; $seen = [];
        for ($col = 1; $col <= $maxCol; $col++) {
            $label = trim((string)self::cellValue($sheet->getCell(Coordinate::stringFromColumnIndex($col).$headerRow)));
            if ($label === '') continue;
            $key = self::normalizeKey($label);
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $headers[] = ['col'=>$col,'text'=>$label];
        }
        if (!$headers) throw new InvalidArgumentException('Excel file has no column headings in the first row.');

        $rows = [];
        for ($r = $headerRow + 1; $r <= $sheet->getHighestRow(); $r++) {
            $item = ['__row'=>$r]; $hasData = false;
            foreach ($headers as $h) {
                $value = self::cellValue($sheet->getCell(Coordinate::stringFromColumnIndex($h['col']).$r));
                $item[$h['text']] = $value;
                if ($value !== '' && $value !== null) $hasData = true;
            }
            if (!$hasData) continue;
            $rows[] = $item;
            if (count($rows) > $maxRows) throw new InvalidArgumentException("The file has more than {$maxRows} rows. Please split it into smaller files.");
        }
        if (!$rows) throw new InvalidArgumentException('Excel file is empty.');
        return ['columns'=>array_column($headers,'text'),'rows'=>$rows];
    }

    private static function findColumn(array $columns, array $wanted): ?string
    {
        $keys = array_map([self::class,'normalizeKey'], $wanted);
        foreach ($columns as $column) if (in_array(self::normalizeKey($column), $keys, true)) return $column;
        return null;
    }

    private static function subjectColumn(array $columns, $subject): ?string
    {
        $name = trim((string)$subject->subject_name);
        $code = trim((string)$subject->subject_code);
        return self::findColumn($columns, [$name,$code,"{$name} ({$code})","{$code} ({$name})"]);
    }

    public static function analyzeRows(array $columns, array $rows, $subjects, array $existing, string $duplicateMode='skip', bool $lockPublished=false): array
    {
        $nameCol = self::findColumn($columns,['Name','Student Name','StudentName','নাম']);
        $rollCol = self::findColumn($columns,['Roll','Roll No','Roll Number','রোল']);
        if (!$nameCol || !$rollCol) throw new InvalidArgumentException('Excel must contain Name and Roll columns.');
        $regCol = self::findColumn($columns,['Registration','Reg','Registration No','Registration Number','রেজিস্ট্রেশন']);
        $groupCol = self::findColumn($columns,['Group','Group Name','গ্রুপ']);
        $subjectCols = [];
        foreach ($subjects as $subject) {
            $col = self::subjectColumn($columns,$subject);
            if (!$col && strtolower((string)$subject->subject_type)!=='fourth') $subjectCols[]=['subject'=>$subject,'col'=>null,'missing'=>true];
            else $subjectCols[]=['subject'=>$subject,'col'=>$col,'missing'=>false];
        }
        $missing = array_map(fn($x)=>$x['subject']->subject_name.' ('.$x['subject']->subject_code.')',array_filter($subjectCols,fn($x)=>$x['missing']));
        if ($missing) throw new InvalidArgumentException('These subject columns are missing in the Excel file: '.implode(', ',$missing));

        $out=[]; $seen=[];
        foreach ($rows as $row) {
            $name=trim((string)($row[$nameCol]??''));
            $roll=trim(self::bnToAsciiDigits($row[$rollCol]??''));
            $registration=$regCol?trim((string)($row[$regCol]??'')):'';
            $group=$groupCol?trim((string)($row[$groupCol]??'')):'';
            $item=['row'=>$row['__row']??null,'name'=>$name,'roll'=>$roll,'registration'=>$registration,'group_name'=>$group,'marks'=>[],'errors'=>[],'state'=>'new','existing_id'=>null,'gpa'=>null,'grade'=>null,'result'=>null];
            if (!$name) $item['errors'][]='Student name is missing.';
            if (!$roll) $item['errors'][]='Roll is missing.';
            if (mb_strlen($name)>200) $item['errors'][]='Student name is too long.';
            foreach ($subjectCols as $entry) {
                $subject=$entry['subject']; $raw=$entry['col']?($row[$entry['col']]??''):'';
                if ($raw===''||$raw===null) { if (strtolower((string)$subject->subject_type)!=='fourth') $item['errors'][]=$subject->subject_name.' marks are missing.'; continue; }
                $marks=is_numeric($raw)?(float)$raw:(float)self::bnToAsciiDigits(trim((string)$raw));
                if (!is_numeric($raw) && !is_numeric(self::bnToAsciiDigits(trim((string)$raw)))) { $item['errors'][]=$subject->subject_name.' marks are not a valid number.'; continue; }
                $full=(float)$subject->full_marks;
                if ($marks<0||$marks>$full) { $item['errors'][]=$subject->subject_name.' marks must be between 0 and '.$full.'.'; continue; }
                $item['marks'][]=['subject_id'=>(int)$subject->id,'marks'=>$marks];
            }
            if ($roll) {
                if (isset($seen[$roll])) $item['errors'][]='Duplicate roll in this file (same as row '.$seen[$roll].').';
                else {
                    $seen[$roll]=$item['row'];
                    if (isset($existing[$roll])) {
                        $found=$existing[$roll]; $item['existing_id']=$found['id']; $item['existing_status']=$found['status'];
                        if ($duplicateMode==='update') {
                            $item['state']='update';
                            if ($lockPublished && $found['status']==='published') $item['errors'][]='This result is already published. Only an admin can change it.';
                        } else { $item['state']='skip'; $item['errors'][]='This roll already exists for this exam (skipped).'; }
                    }
                }
            }
            if (!$item['errors']) {
                try { $graded=GradingService::process($subjects,$item['marks']); $item['gpa']=$graded['final']['gpa']; $item['grade']=$graded['final']['grade']; $item['result']=$graded['final']['status']; }
                catch (\Throwable $e) { $item['errors'][]=$e->getMessage(); }
            }
            $item['valid']=count($item['errors'])===0;
            $out[]=$item;
        }
        return ['rows'=>$out,'counts'=>[
            'total'=>count($out),
            'new'=>count(array_filter($out,fn($x)=>$x['valid']&&$x['state']==='new')),
            'update'=>count(array_filter($out,fn($x)=>$x['valid']&&$x['state']==='update')),
            'skipped'=>count(array_filter($out,fn($x)=>$x['state']==='skip')),
            'invalid'=>count(array_filter($out,fn($x)=>!$x['valid']&&$x['state']!=='skip'))
        ]];
    }

    public static function buildTemplate($subjects=null,$exam=null): Spreadsheet
    {
        $book=new Spreadsheet(); $book->getProperties()->setCreator('School Result System');
        $hasSubjects=$subjects && count($subjects)>0;
        $subjectHeaders=$hasSubjects?array_map(fn($s)=>$s->subject_name.' ('.$s->subject_code.')',$subjects):['Bangla (101)','English (107)','Mathematics (109)'];
        $headers=array_merge(['Name','Roll','Registration','Group'],$subjectHeaders);
        $data=$book->getActiveSheet(); $data->setTitle('Data'); $data->fromArray($headers,null,'A1');
        $data->getStyle('1:1')->getFont()->setBold(true);
        $data->getStyle('1:1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDBEAFE');
        foreach($headers as $i=>$header)$data->getColumnDimension(Coordinate::stringFromColumnIndex($i+1))->setWidth($i===0?28:max(12,mb_strlen((string)$header)+2));
        $data->freezePane('A2');
        $example=$book->createSheet(); $example->setTitle('Example'); $example->fromArray($headers,null,'A1'); $example->getStyle('1:1')->getFont()->setBold(true);
        $sample=$hasSubjects?array_map(fn($s)=>max(0,(int)round((float)$s->full_marks*0.8)),$subjects):[80,80,80];
        $example->fromArray(array_merge(['Sample Student',1,'REG-0001',''],$sample),null,'A2');
        foreach($headers as $i=>$header)$example->getColumnDimension(Coordinate::stringFromColumnIndex($i+1))->setWidth($i===0?28:max(12,mb_strlen((string)$header)+2));
        $help=$book->createSheet(); $help->setTitle('Instructions');
        $lines=['How to fill this file',$exam?'Exam: '.$exam->exam_name.' - Class '.$exam->class_name.' - '.$exam->exam_year:'This is a generic demo. Download again after choosing an exam to get the real subject columns.','1. Enter students in the Data sheet, one student per row, starting at row 2. Do not rename or delete column headings.','2. Name and Roll are required. Registration and Group are optional.','3. Every normal subject column needs marks from 0 to the subject full marks. Fourth subject can be blank.','4. The Example sheet is only an example. Only the first Data sheet is imported.','5. Save as .xlsx (Excel Workbook).'];
        foreach($lines as $line)$help->fromArray([$line],null,'A'.($help->getHighestRow()+1));
        $help->getRowDimension(1)->setRowHeight(24); $help->getStyle('A1')->getFont()->setBold(true)->setSize(14); $help->getColumnDimension('A')->setWidth(110);
        return $book;
    }
}
