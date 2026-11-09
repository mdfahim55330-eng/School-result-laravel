<?php

namespace App\\Services;

use Illuminate\\Support\\Facades\\DB;

class ReportService
{
    private static function compareRoll($a, $b): int
    {
        if (is_numeric($a) && is_numeric($b)) return (float)$a <=> (float)$b;
        return strnatcasecmp((string)$a, (string)$b);
    }

    public static function exam(array $filters): array
    {
        $class = (string)$filters['class_name'];
        $exam = (string)$filters['exam_name'];
        $year = (int)$filters['exam_year'];
        $subjects = DB::table('subjects')->where('class_name', $class)->orderBy('id')->get()->map(fn($x)=>(array)$x)->all();
        $students = DB::table('students')->where(['class_name'=>$class,'exam_name'=>$exam,'exam_year'=>$year])->get()->map(fn($x)=>(array)$x)->all();
        $rows = DB::table('results')->join('students','students.id','=','results.student_id')
            ->where('students.class_name',$class)->where('students.exam_name',$exam)->where('students.exam_year',$year)
            ->select('results.*')->get()->map(fn($x)=>(array)$x)->all();
        $subById = [];
        foreach ($subjects as $s) $subById[$s['id']] = $s;
        $byStudent = [];
        foreach ($rows as $r) $byStudent[$r['student_id']][] = $r;
        $nineTen = in_array(strtolower(trim($class)), ['nine','ten','9','10','ix','x'], true);
        $list = [];
        foreach ($students as $s) {
            $marks=[]; $grades=[]; $total=0; $full=0; $merit=0;
            $studentRows=$byStudent[$s['id']] ?? [];
            foreach ($studentRows as $r) {
                $subject=$subById[$r['subject_id']] ?? null;
                $m=(float)$r['marks'];
                $marks[$r['subject_id']]=$m;
                $grades[$r['subject_id']]=$r['grade'];
                $total += $m;
                $full += (float)($subject['full_marks'] ?? 0);
                $merit += $nineTen && (bool)$r['is_fourth_subject'] ? max(0,$m-40) : $m;
            }
            $final=GradingService::final($studentRows);
            $list[]=[
                'id'=>(int)$s['id'],'name'=>$s['name'],'roll'=>$s['roll'],'registration'=>$s['registration'] ?? '',
                'group_name'=>$s['group_name'] ?? '', 'status'=>$s['status'] ?? 'draft',
                'final_gpa'=>$final['gpa'],'final_grade'=>$final['grade'],'result_status'=>$final['status'],
                'marks'=>$marks,'grades'=>$grades,'subjects_entered'=>count($studentRows),
                'total_marks'=>round($total,2),'total_full_marks'=>$full,'merit_marks'=>round($merit,2),'position'=>null
            ];
        }
        if (!empty($filters['group_name'])) {
            $wanted=$filters['group_name']==='__none__' ? '' : $filters['group_name'];
            $list=array_values(array_filter($list,fn($s)=>$s['group_name']===$wanted));
        }
        $ranked=array_values(array_filter($list,fn($s)=>$s['result_status']==='Pass'));
        $mode=strtolower(env('MERIT_MODE','marks'))==='gpa'?'gpa':'marks';
        usort($ranked,function($a,$b) use($mode) {
            if($mode==='gpa' && round($a['final_gpa'],2)!==round($b['final_gpa'],2)) return $b['final_gpa'] <=> $a['final_gpa'];
            if(round($a['total_marks'],2)!==round($b['total_marks'],2)) return $b['total_marks'] <=> $a['total_marks'];
            return self::compareRoll($a['roll'],$b['roll']);
        });
        $position=0; $prev=null; $positions=[];
        foreach($ranked as $s) {
            $key=(string)round($s['total_marks'],2);
            if($prev===null) $position=1; elseif($key!==$prev) $position++;
            $prev=$key; $positions[$s['id']]=$position;
        }
        foreach($list as &$s) $s['position']=$positions[$s['id']] ?? null;
        unset($s);
        usort($list,function($a,$b) {
            $pa=$a['position']??PHP_INT_MAX; $pb=$b['position']??PHP_INT_MAX;
            return $pa===$pb ? self::compareRoll($a['roll'],$b['roll']) : ($pa <=> $pb);
        });
        return ['meta'=>['class_name'=>$class,'exam_name'=>$exam,'exam_year'=>$year,'group_name'=>$filters['group_name']??'','merit_mode'=>$mode,'total_students'=>count($list)],'subjects'=>$subjects,'students'=>$list];
    }

    public static function statistics(array $report): array
    {
        $students=$report['students']; $subjects=$report['subjects']; $total=count($students);
        $passed=array_values(array_filter($students,fn($s)=>$s['result_status']==='Pass'));
        $gpas=array_column($passed,'final_gpa');
        $grades=array_fill_keys(['A+','A','A-','B','C','D','F'],0);
        foreach($students as $s){$g=$s['result_status']==='Pass'?$s['final_grade']:'F'; if(isset($grades[$g]))$grades[$g]++;}
        $tops=array_values(array_filter($students,fn($s)=>$s['position']===1));
        $subjectStats=[];
        foreach($subjects as $sub){
            $entries=[];
            foreach($students as $s) if(array_key_exists($sub['id'],$s['marks'])) $entries[]=['marks'=>$s['marks'][$sub['id']],'grade'=>$s['grades'][$sub['id']]];
            $marks=array_column($entries,'marks'); $appeared=count($entries); $failed=count(array_filter($entries,fn($e)=>$e['grade']==='F'));
            $subjectStats[]=['subject_id'=>$sub['id'],'subject_name'=>$sub['subject_name'],'subject_code'=>$sub['subject_code'],'full_marks'=>$sub['full_marks'],'subject_type'=>$sub['subject_type'],'appeared'=>$appeared,'passed'=>$appeared-$failed,'failed'=>$failed,'pass_rate'=>$appeared?round((($appeared-$failed)/$appeared)*100,2):0,'highest'=>$marks?max($marks):0,'lowest'=>$marks?min($marks):0,'average'=>$marks?round(array_sum($marks)/count($marks),2):0,'a_plus'=>count(array_filter($entries,fn($e)=>$e['grade']==='A+'))];
        }
        $groups=[];
        foreach($students as $s) $groups[$s['group_name']??''][]=$s;
        $groupStats=[];
        foreach($groups as $name=>$arr){$p=array_values(array_filter($arr,fn($s)=>$s['result_status']==='Pass'));$groupStats[]=['group_name'=>$name,'total'=>count($arr),'passed'=>count($p),'failed'=>count($arr)-count($p),'pass_rate'=>count($arr)?round(count($p)/count($arr)*100,2):0,'average_gpa'=>$p?round(array_sum(array_column($p,'final_gpa'))/count($p),2):0];}
        return ['meta'=>$report['meta'],'summary'=>['total_students'=>$total,'passed'=>count($passed),'failed'=>$total-count($passed),'pass_rate'=>$total?round(count($passed)/$total*100,2):0,'published'=>count(array_filter($students,fn($s)=>$s['status']==='published')),'draft'=>count(array_filter($students,fn($s)=>$s['status']!=='published')),'highest_gpa'=>$gpas?max($gpas):0,'lowest_gpa'=>$gpas?min($gpas):0,'average_gpa'=>$gpas?round(array_sum($gpas)/count($gpas),2):0,'toppers'=>array_map(fn($s)=>['name'=>$s['name'],'roll'=>$s['roll'],'group_name'=>$s['group_name'],'gpa'=>$s['final_gpa']],$tops)],'grades'=>$grades,'subjects'=>$subjectStats,'groups'=>$groupStats];
    }
}
