<?php

require __DIR__.'/../vendor/autoload.php';

use App\Services\ExcelService;
use App\Services\GradingService;

$subject=(object)[
    'id'=>1,
    'subject_name'=>'Bangla',
    'subject_code'=>'101',
    'full_marks'=>100,
    'subject_type'=>'main',
];

$grade=GradingService::grade(80,100);
if($grade['grade']!=='A+' || $grade['point']!==5.0) {
    throw new RuntimeException('GPA grade threshold smoke test failed.');
}

$analysis=ExcelService::analyzeRows(
    ['Name','Roll','Bangla (101)'],
    [['__row'=>2,'Name'=>'Sample Student','Roll'=>'০১','Bangla (101)'=>80]],
    [$subject],
    [],
    'skip',
    false
);
if(!$analysis['rows'][0]['valid'] || $analysis['counts']['new']!==1 || $analysis['rows'][0]['roll']!=='01') {
    throw new RuntimeException('Excel row analysis smoke test failed.');
}

$book=ExcelService::buildTemplate(
    [$subject],
    (object)['exam_name'=>'Sample Exam','class_name'=>'Six','exam_year'=>2026]
);
if($book->getSheetCount()!==3 || $book->getSheet(0)->getTitle()!=='Data') {
    throw new RuntimeException('Excel template generation smoke test failed.');
}

echo "Grading, Bengali roll normalization, Excel analysis and template smoke tests passed.\n";
