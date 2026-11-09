<?php
return [
 'name'=>env('SCHOOL_NAME','Shaheed Nur Hossain Memorial School'),
 'address'=>env('SCHOOL_ADDRESS','Biral, Dinajpur'),
 'eiin'=>env('SCHOOL_EIIN',''),
 'merit_mode'=>strtolower(env('MERIT_MODE','marks'))==='gpa'?'gpa':'marks',
 'verify_secret'=>env('VERIFY_SECRET') ?: env('APP_KEY',''),
 'upload_max_mb'=>(int)env('UPLOAD_MAX_MB',5),
];
