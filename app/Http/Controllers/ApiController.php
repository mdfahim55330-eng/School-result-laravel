<?php

namespace App\\Http\\Controllers;

use App\\Services\\GradingService;
use App\\Services\\ReportService;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Hash;
use Illuminate\\Support\\Facades\\URL;
use PhpOffice\\PhpSpreadsheet\\IOFactory;
use PhpOffice\\PhpSpreadsheet\\Spreadsheet;
use PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx;
use Barryvdh\\DomPDF\\Facade\\Pdf;
use BaconQrCode\\Renderer\\ImageRenderer;
use BaconQrCode\\Renderer\\Image\\SvgImageBackEnd;
use BaconQrCode\\Renderer\\RendererStyle\\RendererStyle;
use BaconQrCode\\Writer;

class ApiController
{
    private function staff(Request $r) { return $r->attributes->get('staff'); }
    private function admin(Request $r) {
        $u=$this->staff($r);
        if (!$u || $u->role!=='admin') return response()->json(['success'=>false,'message'=>'You do not have permission to do this. Please ask an admin.'],403);
        return null;
    }
    private function audit(Request $r,string $action,string $entity='', $id=null,string $summary='',array $details=[]): void {
        $u=$this->staff($r);
        DB::table('audit_log')->insert(['admin_id'=>$u->id??null,'username'=>$u->username??null,'role'=>$u->role??null,'action'=>$action,'entity'=>$entity?:null,'entity_id'=>$id===null?null:(string)$id,'summary'=>$summary,'details'=>$details?json_encode($details,JSON_UNESCAPED_UNICODE):null,'ip'=>$r->ip(),'created_at'=>now()]);
    }
    private function ok(string $message='OK',array $extra=[]){return response()->json(array_merge(['success'=>true,'message'=>$message],$extra));}
    private function fail(string $message,int $status=400){return response()->json(['success'=>false,'message'=>$message],$status);}
    private function school(): array { return ['name'=>config('school.name'),'address'=>config('school.address'),'eiin'=>config('school.eiin')]; }
    private function verifyUrl($id): string {
        $secret = config('school.verify_secret') ?: config('app.key');
        $sig = hash_hmac('sha256', (string)$id, (string)$secret);
        return URL::to('/verify/'.$id.'/'.$sig);
    }
    private function qrData(string $url): string {
        $renderer = new ImageRenderer(new RendererStyle(180), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($url);
        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    // Authentication
    public function me(Request $r) {
        $id=$r->session()->get('admin_id'); if(!$id) return response()->json(['loggedIn'=>false]);
        $u=DB::table('admins')->where('id',$id)->first();
        if(!$u || !$u->active) return response()->json(['loggedIn'=>false]);
        return response()->json(['loggedIn'=>true,'username'=>$u->username,'role'=>$u->role,'mustChangePassword'=>(bool)$u->must_change_password]);
    }
    public function login(Request $r) {
        $v=$r->validate(['username'=>'required|string|max:100','password'=>'required|string|max:200']);
        $u=DB::table('admins')->where('username',trim($v['username']))->first();
        if(!$u || !$u->active || !Hash::check($v['password'],$u->password)) {
            DB::table('audit_log')->insert(['username'=>substr($v['username'],0,60),'action'=>'login_failed','entity'=>'admin','summary'=>'Failed login attempt','ip'=>$r->ip(),'created_at'=>now()]);
            return $this->fail('Invalid username or password.',401);
        }
        $r->session()->regenerate(); $r->session()->put(['admin_id'=>$u->id,'admin_username'=>$u->username,'role'=>$u->role]);
        DB::table('admins')->where('id',$u->id)->update(['last_login'=>now()]);
        $r->attributes->set('staff',$u); $this->audit($r,'login','admin',$u->id,$u->username.' logged in');
        return $this->ok('Login successful.',['username'=>$u->username,'role'=>$u->role,'must_change_password'=>(bool)$u->must_change_password]);
    }
    public function logout(Request $r) { $r->session()->invalidate(); $r->session()->regenerateToken(); return $this->ok('Logout successful.'); }
    public function changePassword(Request $r) {
        $u=$this->staff($r); if(!$u) return $this->fail('Admin login required.',401);
        $v=$r->validate(['current_password'=>'required|string','new_password'=>'required|string|min:8|max:200']);
        $dbu=DB::table('admins')->where('id',$u->id)->first();
        if(!Hash::check($v['current_password'],$dbu->password)) return $this->fail('Current password is not correct.');
        if($v['new_password']===$v['current_password'] || strtolower($v['new_password'])===strtolower($u->username)) return $this->fail('The new password must be different from the current password and username.');
        DB::table('admins')->where('id',$u->id)->update(['password'=>Hash::make($v['new_password']),'must_change_password'=>false,'updated_at'=>now()]);
        $this->audit($r,'password_changed','admin',$u->id,$u->username.' changed their password');
        return $this->ok('Password changed successfully.');
    }

    // Classes, subjects, exams
    public function classes() { return response()->json(['success'=>true,'classes'=>DB::table('classes')->orderBy('class_name')->get()]); }
    public function storeClass(Request $r) {
        if($x=$this->admin($r)) return $x; $v=$r->validate(['class_name'=>'required|string|max:50']);
        if(DB::table('classes')->whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($v['class_name']))])->exists()) return $this->fail('This class already exists.');
        $id=DB::table('classes')->insertGetId(['class_name'=>trim($v['class_name']),'created_at'=>now(),'updated_at'=>now()]);
        $this->audit($r,'class_created','class',$id,'Added class '.$v['class_name'],$v); return $this->ok('Class added successfully.',['id'=>$id]);
    }
    public function deleteClass(Request $r,$id) {
        if($x=$this->admin($r)) return $x; $item=DB::table('classes')->where('id',$id)->first(); if(!$item)return $this->fail('Class not found.',404);
        $used=DB::table('students')->where('class_name',$item->class_name)->count()+DB::table('exams')->where('class_name',$item->class_name)->count()+DB::table('subjects')->where('class_name',$item->class_name)->count();
        if($used)return $this->fail('This class is already used by existing students, exams or subjects. Delete those records first.',409);
        DB::table('classes')->where('id',$id)->delete(); $this->audit($r,'class_deleted','class',$id,'Deleted class '.$item->class_name); return $this->ok('Class deleted successfully.');
    }
    public function subjects() { return response()->json(['success'=>true,'subjects'=>DB::table('subjects')->orderBy('class_name')->orderBy('subject_code')->get()]); }
    public function storeSubject(Request $r) {
        if($x=$this->admin($r))return $x;
        $v=$r->validate(['class_name'=>'required|string|max:50','subject_name'=>'required|string|max:150','subject_code'=>'required|string|max:30','full_marks'=>'required|integer|min:1|max:1000','subject_type'=>'nullable|in:main,fourth']);
        if(DB::table('subjects')->where('class_name',$v['class_name'])->where('subject_code',$v['subject_code'])->exists())return $this->fail('This subject already exists for this class.');
        $id=DB::table('subjects')->insertGetId($v+['subject_type'=>$v['subject_type']??'main','created_at'=>now(),'updated_at'=>now()]);
        $this->audit($r,'subject_created','subject',$id,'Added subject '.$v['subject_name'],$v); return $this->ok('Subject added successfully.',['id'=>$id]);
    }
    public function deleteSubject(Request $r,$id) {
        if($x=$this->admin($r))return $x; $s=DB::table('subjects')->where('id',$id)->first(); if(!$s)return $this->fail('Subject not found.',404);
        $used=DB::table('results')->where('subject_id',$id)->count(); if($used && strtolower((string)$r->query('force'))!=='true')return response()->json(['success'=>false,'needs_force'=>true,'message'=>sprintf('"%s" already has marks saved for %d result(s). If you delete it, those marks are removed permanently and every affected GPA is recalculated.',$s->subject_name,$used)],409);
        $affected=DB::table('results')->where('subject_id',$id)->pluck('student_id')->unique()->all();
        DB::transaction(function()use($id,$affected){DB::table('subjects')->where('id',$id)->delete();foreach($affected as $sid){$rows=DB::table('results')->where('student_id',$sid)->get()->map(fn($x)=>(array)$x)->all();$f=GradingService::final($rows);DB::table('students')->where('id',$sid)->update(['final_gpa'=>$f['gpa'],'final_grade'=>$f['grade'],'result_status'=>$f['status'],'updated_at'=>now()]);}});
        $this->audit($r,'subject_deleted','subject',$id,'Deleted subject '.$s->subject_name,['results_removed'=>$used]);return $this->ok('Subject deleted successfully.');
    }
    public function exams() { return response()->json(['success'=>true,'exams'=>DB::table('exams')->orderByDesc('exam_year')->orderByDesc('id')->get()]); }
    public function storeExam(Request $r) {
        if($x=$this->admin($r))return $x; $v=$r->validate(['exam_name'=>'required|string|max:150','exam_year'=>'required|integer|min:2000|max:2100','class_name'=>'required|string|max:50']);
        if(DB::table('exams')->where($v)->exists())return $this->fail('This exam already exists for this class and year.');
        $id=DB::table('exams')->insertGetId($v+['created_at'=>now(),'updated_at'=>now()]);$this->audit($r,'exam_created','exam',$id,'Added exam '.$v['exam_name']);return $this->ok('Exam added successfully.',['id'=>$id]);
    }
    public function deleteExam(Request $r,$id) {
        if($x=$this->admin($r))return $x; $e=DB::table('exams')->where('id',$id)->first();DB::table('exams')->where('id',$id)->delete();
        if($e)$this->audit($r,'exam_deleted','exam',$id,'Deleted exam '.$e->exam_name.' '.$e->exam_year);return $this->ok('Exam deleted successfully.');
    }

    // Result helpers
    private function exam($id){return DB::table('exams')->where('id',$id)->first();}
    private function subjectsFor($class){return DB::table('subjects')->where('class_name',$class)->orderBy('id')->get();}
    private function saveRows(int $studentId,array $rows): void {
        DB::table('results')->where('student_id',$studentId)->delete();
        foreach($rows as $x) DB::table('results')->insert($x+['student_id'=>$studentId,'created_at'=>now(),'updated_at'=>now()]);
    }
    private function resultData($s): array {
        $subs=DB::table('results')->join('subjects','subjects.id','=','results.subject_id')->where('results.student_id',$s->id)->orderBy('subjects.id')->select('results.id','results.subject_id','results.marks','results.grade','results.grade_point','results.is_fourth_subject','subjects.subject_name','subjects.subject_code','subjects.full_marks','subjects.subject_type')->get();
        $rows=$subs->map(fn($x)=>(array)$x)->all();$final=GradingService::final($rows);$report=ReportService::exam(['class_name'=>$s->class_name,'exam_name'=>$s->exam_name,'exam_year'=>$s->exam_year]);$me=collect($report['students'])->firstWhere('id',(int)$s->id);
        return ['student'=>['id'=>$s->id,'name'=>$s->name,'roll'=>$s->roll,'registration'=>$s->registration,'class_name'=>$s->class_name,'group_name'=>$s->group_name,'exam_name'=>$s->exam_name,'exam_year'=>$s->exam_year,'institute_name'=>$s->institute_name?:$this->school()['name'],'eiin'=>$s->eiin?:($this->school()['eiin']?:null)],'school'=>$this->school(),'subjects'=>$rows,'summary'=>['total_marks'=>array_sum(array_column($rows,'marks')),'total_full_marks'=>array_sum(array_column($rows,'full_marks')),'gpa'=>$final['gpa'],'grade'=>$final['grade'],'result'=>$final['status'],'position'=>$me['position']??'-']];
    }
    public function results(Request $r) {
        $q=DB::table('students');foreach(['class_name','exam_name','exam_year','status'] as $k)if($r->filled($k))$q->where($k,$r->query($k));
        if($r->filled('search')){$v='%'.$r->query('search').'%';$q->where(fn($w)=>$w->where('name','like',$v)->orWhere('roll','like',$v)->orWhere('registration','like',$v));}
        $rows=$q->orderByDesc('id')->limit(20000)->get()->map(function($s){$a=(array)$s;$a['position']=null;$a['failed_subjects']='';return $a;})->all();
        if($r->filled('class_name')&&$r->filled('exam_name')&&$r->filled('exam_year')){$rep=ReportService::exam(['class_name'=>$r->query('class_name'),'exam_name'=>$r->query('exam_name'),'exam_year'=>$r->query('exam_year')]);$idx=collect($rep['students'])->keyBy('id');foreach($rows as &$row){$x=$idx->get($row['id']);$row['position']=$x['position']??null;$failed=[];foreach($rep['subjects'] as $s)if(($x['grades'][$s['id']]??'')==='F')$failed[]=$s['subject_name'];$row['failed_subjects']=implode(', ',$failed);}unset($row);}
        return response()->json(['success'=>true,'results'=>$rows]);
    }
    public function showResult(Request $r,$id) {
        $s=DB::table('students')->where('id',$id)->first();if(!$s)return $this->fail('Result not found.',404);
        $data=$this->resultData($s);return response()->json(['success'=>true,'student'=>$data['student']+['status'=>$s->status,'final_gpa'=>$s->final_gpa,'final_grade'=>$s->final_grade,'result_status'=>$s->result_status],'results'=>$data['subjects']]);
    }
    public function storeResult(Request $r) {
        $v=$r->validate(['exam_id'=>'required|integer','student.name'=>'required|string|max:200','student.roll'=>'required|string|max:50','student.registration'=>'nullable|string|max:100','student.group_name'=>'nullable|string|max:50','results'=>'required|array|min:1','results.*.subject_id'=>'required|integer','results.*.marks'=>'required']);
        $exam=$this->exam($v['exam_id']);if(!$exam)return $this->fail('Exam not found.',404);
        $subjects=$this->subjectsFor($exam->class_name);if(!$subjects->count())return $this->fail('No subjects found for Class '.$exam->class_name.'.');
        try{$graded=GradingService::process($subjects,$v['results']);}catch(\\Throwable $e){return $this->fail($e->getMessage());}
        $entered=collect($graded['rows'])->pluck('subject_id')->all();$missing=$subjects->filter(fn($s)=>$s->subject_type!=='fourth'&&!in_array($s->id,$entered))->pluck('subject_name')->all();if($missing)return $this->fail('Marks are missing for: '.implode(', ',$missing).'.');
        $st=$v['student'];$st['roll']=trim($st['roll']);
        if(DB::table('students')->where(['class_name'=>$exam->class_name,'exam_name'=>$exam->exam_name,'exam_year'=>$exam->exam_year,'roll'=>$st['roll']])->exists())return $this->fail('Roll '.$st['roll'].' already exists for this class, exam and year.',409);
        $id=DB::transaction(function()use($exam,$st,$graded){$sid=DB::table('students')->insertGetId(['name'=>$st['name'],'roll'=>$st['roll'],'registration'=>$st['registration']??'','group_name'=>$st['group_name']??'','class_name'=>$exam->class_name,'exam_name'=>$exam->exam_name,'exam_year'=>$exam->exam_year,'final_gpa'=>$graded['final']['gpa'],'final_grade'=>$graded['final']['grade'],'result_status'=>$graded['final']['status'],'status'=>'draft','created_at'=>now(),'updated_at'=>now()]);$this->saveRows($sid,$graded['rows']);return $sid;});
        $this->audit($r,'result_created','student',$id,'Saved result of '.$st['name'].' (roll '.$st['roll'].')');return $this->ok('Result saved successfully.',['student_id'=>$id,'gpa'=>$graded['final']['gpa'],'grade'=>$graded['final']['grade'],'result'=>$graded['final']['status']]);
    }
    public function updateResult(Request $r,$id) {
        $v=$r->validate(['student.name'=>'required|string|max:200','student.roll'=>'required|string|max:50','student.registration'=>'nullable|string|max:100','student.group_name'=>'nullable|string|max:50','results'=>'required|array|min:1','results.*.subject_id'=>'required|integer','results.*.marks'=>'required']);
        $s=DB::table('students')->where('id',$id)->first();if(!$s)return $this->fail('Student result not found.',404);
        $u=$this->staff($r);if($u->role!=='admin'&&$s->status==='published')return $this->fail('Only an admin can edit a published result. Ask an admin to unpublish it first.',403);
        $subjects=$this->subjectsFor($s->class_name);try{$g=GradingService::process($subjects,$v['results']);}catch(\\Throwable $e){return $this->fail($e->getMessage());}
        $ids=collect($g['rows'])->pluck('subject_id')->all();$missing=$subjects->filter(fn($x)=>$x->subject_type!=='fourth'&&!in_array($x->id,$ids))->pluck('subject_name')->all();if($missing)return $this->fail('Marks are missing for: '.implode(', ',$missing).'.');
        $st=$v['student'];if(DB::table('students')->where(['class_name'=>$s->class_name,'exam_name'=>$s->exam_name,'exam_year'=>$s->exam_year,'roll'=>trim($st['roll'])])->where('id','<>',$id)->exists())return $this->fail('Roll already exists for this class, exam and year.',409);
        DB::transaction(function()use($id,$st,$g){DB::table('students')->where('id',$id)->update(['name'=>$st['name'],'roll'=>trim($st['roll']),'registration'=>$st['registration']??'','group_name'=>$st['group_name']??'','final_gpa'=>$g['final']['gpa'],'final_grade'=>$g['final']['grade'],'result_status'=>$g['final']['status'],'updated_at'=>now()]);$this->saveRows($id,$g['rows']);});
        $this->audit($r,'result_updated','student',$id,'Edited result of '.$st['name'].' (roll '.$st['roll'].')');return $this->ok('Result updated successfully.',['final_gpa'=>$g['final']['gpa'],'final_grade'=>$g['final']['grade'],'result_status'=>$g['final']['status']]);
    }
    public function deleteResult(Request $r,$id) {if($x=$this->admin($r))return $x;$s=DB::table('students')->where('id',$id)->first();if(!$s)return $this->fail('Result not found.',404);DB::table('students')->where('id',$id)->delete();$this->audit($r,'result_deleted','student',$id,'Deleted result of '.$s->name.' (roll '.$s->roll.')');return $this->ok('Result deleted successfully.');}
    public function publishResult(Request $r,$id) {
        if($x=$this->admin($r))return $x;$s=DB::table('students')->where('id',$id)->first();if(!$s)return $this->fail('Result not found.',404);
        $subs=$this->subjectsFor($s->class_name);$ids=DB::table('results')->where('student_id',$id)->pluck('subject_id')->all();$missing=$subs->filter(fn($a)=>$a->subject_type!=='fourth'&&!in_array($a->id,$ids))->pluck('subject_name')->all();if($missing)return $this->fail('Cannot publish: marks are missing for '.implode(', ',$missing).'.');
        DB::table('students')->where('id',$id)->update(['status'=>'published','updated_at'=>now()]);$this->audit($r,'result_published','student',$id,'Published result of '.$s->name);return $this->ok('Result published successfully.');
    }
    public function unpublishResult(Request $r,$id) {if($x=$this->admin($r))return $x;$s=DB::table('students')->where('id',$id)->first();if(!$s)return $this->fail('Result not found.',404);DB::table('students')->where('id',$id)->update(['status'=>'draft','updated_at'=>now()]);$this->audit($r,'result_unpublished','student',$id,'Unpublished result of '.$s->name);return $this->ok('Result moved to draft.');}
    public function dashboardStats() {return response()->json(['success'=>true,'totalStudents'=>DB::table('students')->count(),'totalResults'=>DB::table('results')->distinct('student_id')->count('student_id'),'publishedResults'=>DB::table('students')->where('status','published')->count(),'draftResults'=>DB::table('students')->where('status','draft')->count()]);}
    public function bulkDelete(Request $r) {
        if($x=$this->admin($r))return $x;$v=$r->validate(['class_name'=>'required','exam_name'=>'required','exam_year'=>'required|integer']);$n=DB::table('students')->where($v)->delete();if(!$n)return $this->fail('No results found for the selected Class, Exam and Year.',404);$this->audit($r,'results_bulk_deleted','exam',null,'Deleted '.$n.' results',$v);return $this->ok('All selected results deleted successfully.',['deleted_count'=>$n]);
    }
    public function publishBulk(Request $r) {
        if($x=$this->admin($r))return $x;$v=$r->validate(['class_name'=>'required','exam_name'=>'required','exam_year'=>'required|integer','action'=>'required|in:publish,unpublish']);
        $students=DB::table('students')->where(['class_name'=>$v['class_name'],'exam_name'=>$v['exam_name'],'exam_year'=>$v['exam_year']])->get();if(!$students->count())return $this->fail('No results found for the selected Class, Exam and Year.',404);
        $changed=0;$skipped=0;
        DB::transaction(function()use($v,$students,&$changed,&$skipped){foreach($students as $s){if($v['action']==='unpublish'){if($s->status==='published'){$changed++;DB::table('students')->where('id',$s->id)->update(['status'=>'draft','updated_at'=>now()]);}}else{$subs=DB::table('subjects')->where('class_name',$s->class_name)->where('subject_type','<>','fourth')->pluck('id')->all();$have=DB::table('results')->where('student_id',$s->id)->pluck('subject_id')->all();if(!array_diff($subs,$have)){if($s->status!=='published')$changed++;DB::table('students')->where('id',$s->id)->update(['status'=>'published','updated_at'=>now()]);}else $skipped++;}}});
        $message=$v['action']==='publish'?$changed.' result(s) published.':$changed.' result(s) moved back to draft.';if($skipped)$message.=' '.$skipped.' result(s) were NOT published because marks are missing for some subjects.';
        $this->audit($r,'results_bulk_'.$v['action'],'exam',null,$message);return $this->ok($message,['total'=>$students->count(),'changed'=>$changed,'skipped_incomplete'=>$skipped]);
    }

    // Reports
    public function reportOptions() {
        $rows=DB::table('students')->select('class_name','exam_name','exam_year',DB::raw('COUNT(*) as students'),DB::raw("SUM(CASE WHEN status='published' THEN 1 ELSE 0 END) as published"))->groupBy('class_name','exam_name','exam_year')->orderByDesc('exam_year')->orderBy('class_name')->orderBy('exam_name')->get();
        return response()->json(['success'=>true,'options'=>$rows]);
    }
    private function report(Request $r) { $v=$r->validate(['class_name'=>'required|string','exam_name'=>'required|string','exam_year'=>'required|integer','group_name'=>'nullable|string']);return ReportService::exam($v); }
    public function merit(Request $r) {$d=$this->report($r);$students=array_map(fn($s)=>array_intersect_key($s,array_flip(['position','id','roll','name','group_name','status','final_gpa','final_grade','result_status','total_marks','total_full_marks','merit_marks'])),$d['students']);return response()->json(['success'=>true,'meta'=>$d['meta'],'students'=>$students]);}
    public function tabulation(Request $r) {return response()->json(['success'=>true]+$this->report($r)+['school'=>$this->school()]);}
    public function statistics(Request $r) {$d=$this->report($r);return response()->json(['success'=>true]+ReportService::statistics($d));}

    // Public result search and printable marksheet
    public function publicOptions() {$rows=DB::table('students')->where('status','published')->select('class_name','exam_name','exam_year')->groupBy('class_name','exam_name','exam_year')->orderBy('class_name')->orderByDesc('exam_year')->get();return response()->json(['success'=>true,'options'=>$rows]);}
    private function findPublished(Request $r) {
        $v=$r->validate(['roll'=>'required|string|max:50','class_name'=>'required|string|max:50','exam_name'=>'required|string|max:150','exam_year'=>'required|integer']);
        $rows=DB::table('students')->where($v)->where('status','published')->get();if(!$rows->count())return null;if($rows->count()>1)return false;return $rows->first();
    }
    public function publicSearch(Request $r) { $s=$this->findPublished($r); if($s===false)return $this->fail('More than one result was found for this roll. Please contact the school office.',409); if(!$s)return $this->fail('Result not found or result has not been published.',404); $data=$this->resultData($s); $data['verify_url']=$this->verifyUrl($s->id); $data['qr']=$this->qrData($data['verify_url']); return response()->json(['success'=>true]+$data); }
    public function publicPdf(Request $r) { $s=$this->findPublished($r); if(!$s)return $this->fail('Result not found or result has not been published.',404); $d=$this->resultData($s); $d['verify_url']=$this->verifyUrl($s->id); $d['qr']=$this->qrData($d['verify_url']); return Pdf::loadView('marksheet-pdf',$d)->setPaper('a4')->stream('marksheet-'.$s->roll.'-'.$s->exam_year.'.pdf'); }
    public function classPdf(Request $r) { $d=$this->report($r); $items=array_values(array_filter($d['students'],fn($s)=>$r->query('status','published')==='all'||$s['status']==='published')); if(!$items)return $this->fail('No published results found for this selection.',404); if(count($items)>600)return $this->fail('Too many students for one PDF. Please choose a smaller group.'); $pages=[]; foreach($items as $item){$s=DB::table('students')->where('id',$item['id'])->first();$page=$this->resultData($s);$page['verify_url']=$this->verifyUrl($s->id);$page['qr']=$this->qrData($page['verify_url']);$pages[]=$page;} return Pdf::loadView('class-marksheets',['pages'=>$pages])->setPaper('a4')->stream('class-marksheets.pdf'); }

    // Users and audit
    public function users(Request $r) { if($x=$this->admin($r))return $x; return response()->json(['success'=>true,'users'=>DB::table('admins')->select('id','username','role','active','must_change_password','last_login','created_at')->orderBy('id')->get()]); }
    public function storeUser(Request $r) {
        if($x=$this->admin($r))return $x;$v=$r->validate(['username'=>'required|string|regex:/^[A-Za-z0-9._-]{3,50}$/','password'=>'required|string|min:8|max:200','role'=>'required|in:admin,teacher']);
        if(strtolower($v['password'])===strtolower($v['username']))return $this->fail('The password must not be the same as the username.');if(DB::table('admins')->whereRaw('LOWER(username)=?', [strtolower($v['username'])])->exists())return $this->fail('This username is already taken.');
        $id=DB::table('admins')->insertGetId(['username'=>$v['username'],'password'=>Hash::make($v['password']),'role'=>$v['role'],'must_change_password'=>true,'active'=>true,'created_at'=>now(),'updated_at'=>now()]);$this->audit($r,'user_created','admin',$id,'Created '.$v['role'].' account '.$v['username']);return $this->ok('User created. They must set a new password at first login.',['id'=>$id]);
    }
    public function updateUser(Request $r,$id) {
        if($x=$this->admin($r))return $x;$target=DB::table('admins')->where('id',$id)->first();if(!$target)return $this->fail('User not found.',404);$v=$r->validate(['role'=>'nullable|in:admin,teacher','active'=>'nullable|boolean']);$role=$v['role']??$target->role;$active=$v['active']??$target->active;$me=$this->staff($r);
        if((int)$id===(int)$me->id&&($role!=='admin'||!$active))return $this->fail('You cannot remove your own admin access or disable your own account.');
        if($target->role==='admin'&&$target->active&&($role!=='admin'||!$active)&&DB::table('admins')->where('role','admin')->where('active',true)->where('id','<>',$id)->count()===0)return $this->fail('There must be at least one active admin.');
        DB::table('admins')->where('id',$id)->update(['role'=>$role,'active'=>$active,'updated_at'=>now()]);$this->audit($r,'user_updated','admin',$id,'Updated account '.$target->username);return $this->ok('User updated.');
    }
    public function resetPassword(Request $r,$id) {if($x=$this->admin($r))return $x;$v=$r->validate(['new_password'=>'required|string|min:8|max:200']);$u=DB::table('admins')->where('id',$id)->first();if(!$u)return $this->fail('User not found.',404);if(strtolower($v['new_password'])===strtolower($u->username))return $this->fail('The password must not be the same as the username.');DB::table('admins')->where('id',$id)->update(['password'=>Hash::make($v['new_password']),'must_change_password'=>true,'updated_at'=>now()]);$this->audit($r,'password_reset','admin',$id,'Reset password for '.$u->username);return $this->ok('Password reset. The user must choose a new one at next login.');}
    public function auditLog(Request $r) { if($x=$this->admin($r))return $x; $limit=min(200,max(1,(int)$r->query('limit',50)));$offset=max(0,(int)$r->query('offset',0));$q=DB::table('audit_log');if($r->filled('action'))$q->where('action',$r->query('action'));if($r->filled('q'))$q->where(fn($w)=>$w->where('summary','like','%'.$r->query('q').'%')->orWhere('username','like','%'.$r->query('q').'%'));$total=(clone $q)->count();$logs=$q->orderByDesc('id')->offset($offset)->limit($limit)->get();$actions=DB::table('audit_log')->distinct()->orderBy('action')->pluck('action');return response()->json(['success'=>true,'total'=>$total,'logs'=>$logs,'actions'=>$actions]);}

    // Excel bulk upload
    private function readExcel($file): array {
        $book=IOFactory::load($file->getRealPath());$sheet=$book->getActiveSheet();$rows=$sheet->toArray(null,true,true,true);if(count($rows)<2)return ['columns'=>[],'rows'=>[]];
        $headers=array_map(fn($x)=>trim((string)$x),array_shift($rows));$out=[];foreach($rows as $index=>$row){$x=['__row'=>$index+2];foreach($headers as $col=>$name)if($name!=='')$x[$name]=$row[$col]??null;if(array_filter($x,fn($v,$k)=>$k!=='__row'&&$v!==null&&$v!=='',ARRAY_FILTER_USE_BOTH))$out[]=$x;}
        return ['columns'=>array_values(array_filter($headers)),'rows'=>$out];
    }
    private function normalizeDigits($value): string {
        return strtr((string)$value, ['০'=>'0','১'=>'1','২'=>'2','৩'=>'3','৪'=>'4','৫'=>'5','৬'=>'6','৭'=>'7','৮'=>'8','৯'=>'9']);
    }
    private function excelRows($file,$exam,$mode='skip',$lockPublished=false): array {
        $sheet=$this->readExcel($file); $subs=$this->subjectsFor($exam->class_name); $out=[];
        foreach($sheet['rows'] as $row) {
            $name=trim((string)($row['Name']??$row['Student Name']??''));
            $roll=trim($this->normalizeDigits($row['Roll']??$row['Roll No']??''));
            $errs=[]; $marks=[];
            if($name==='') $errs[]='Student name is required.';
            if($roll==='') $errs[]='Roll is required.';
            foreach($subs as $s) {
                $value=$row[$s->subject_code]??$row[$s->subject_name]??null;
                if($value===''||$value===null) { if($s->subject_type!=='fourth') $errs[]=$s->subject_name.' marks are required.'; continue; }
                $marks[]=['subject_id'=>$s->id,'marks'=>$this->normalizeDigits($value)];
            }
            $old=DB::table('students')->where(['class_name'=>$exam->class_name,'exam_name'=>$exam->exam_name,'exam_year'=>$exam->exam_year,'roll'=>$roll])->first();
            $state=$old ? (($mode==='update' && !($lockPublished && $old->status==='published'))?'update':'skip') : 'create';
            if($old && $mode!=='update') $errs[]='Duplicate roll already exists.';
            if($old && $lockPublished && $old->status==='published') $errs[]='Published results can only be updated by an admin.';
            try { $graded=$marks?GradingService::process($subs,$marks):null; }
            catch(\\Throwable $e) { $errs[]=$e->getMessage(); $graded=null; }
            $valid=!$errs && $graded;
            $out[]=['row'=>$row['__row'],'name'=>$name,'roll'=>$roll,'valid'=>(bool)$valid,'state'=>$state,'errors'=>$errs,'gpa'=>$graded['final']['gpa']??null,'grade'=>$graded['final']['grade']??null,'result'=>$graded['final']['status']??null,'marks'=>$graded['rows']??[],'existing_id'=>$old->id??null,'registration'=>$row['Registration']??'','group_name'=>$row['Group']??''];
        }
        $new=count(array_filter($out,fn($x)=>$x['valid'] && $x['state']==='create'));
        $update=count(array_filter($out,fn($x)=>$x['valid'] && $x['state']==='update'));
        $skipped=count(array_filter($out,fn($x)=>$x['state']==='skip'));
        $invalid=count(array_filter($out,fn($x)=>!$x['valid'] && $x['state']!=='skip'));
        return ['sheet'=>$sheet,'rows'=>$out,'counts'=>['total'=>count($out),'new'=>$new,'update'=>$update,'skipped'=>$skipped,'invalid'=>$invalid,'valid'=>$new+$update,'create'=>$new]];
    }
    public function bulkPreview(Request $r) {
        $r->validate(['excelFile'=>'required|file|mimes:xlsx|max:5120']);$file=$r->file('excelFile');$examId=(int)$r->input('exam_id');
        if(!$examId){$s=$this->readExcel($file);return response()->json(['success'=>true,'total_rows'=>count($s['rows']),'columns'=>$s['columns'],'rows'=>$s['rows']]);}
        $exam=$this->exam($examId);if(!$exam)return $this->fail('Exam not found.',404);$a=$this->excelRows($file,$exam,$r->input('duplicate_mode','skip'),$this->staff($r)->role!=='admin');
        return response()->json(['success'=>true,'total_rows'=>count($a['rows']),'columns'=>$a['sheet']['columns'],'rows'=>array_map(fn($x)=>array_diff_key($x,['marks'=>true,'existing_id'=>true]),$a['rows']),'exam'=>['id'=>$exam->id,'exam_name'=>$exam->exam_name,'exam_year'=>$exam->exam_year,'class_name'=>$exam->class_name],'counts'=>$a['counts'],'analysis'=>$a['rows']]);
    }
    public function bulkImport(Request $r) {
        $r->validate(['excelFile'=>'required|file|mimes:xlsx|max:5120','exam_id'=>'required|integer']);$exam=$this->exam($r->input('exam_id'));if(!$exam)return $this->fail('Exam not found.',404);
        $a=$this->excelRows($r->file('excelFile'),$exam,$r->input('duplicate_mode','skip'),$this->staff($r)->role!=='admin');$u=$this->staff($r);$publish=$u->role==='admin'&&strtolower((string)$r->input('publish','true'))!=='false';$created=0;$updated=0;
        DB::transaction(function()use($a,$exam,$publish,&$created,&$updated){foreach($a['rows'] as $x){if(!$x['valid'])continue;$st=DB::table('students')->where('id',$x['existing_id'])->first();if($st){DB::table('students')->where('id',$st->id)->update(['name'=>$x['name'],'registration'=>$x['registration'],'group_name'=>$x['group_name'],'final_gpa'=>$x['gpa'],'final_grade'=>$x['grade'],'result_status'=>$x['result'],'status'=>$publish?'published':$st->status,'updated_at'=>now()]);$this->saveRows($st->id,$x['marks']);$updated++;}else{$id=DB::table('students')->insertGetId(['name'=>$x['name'],'roll'=>$x['roll'],'registration'=>$x['registration'],'group_name'=>$x['group_name'],'class_name'=>$exam->class_name,'exam_name'=>$exam->exam_name,'exam_year'=>$exam->exam_year,'final_gpa'=>$x['gpa'],'final_grade'=>$x['grade'],'result_status'=>$x['result'],'status'=>$publish?'published':'draft','created_at'=>now(),'updated_at'=>now()]);$this->saveRows($id,$x['marks']);$created++;}}});
        $errors=[];foreach($a['rows'] as $x)if(!$x['valid'])$errors[]=['row'=>$x['row'],'name'=>$x['name'],'roll'=>$x['roll'],'error'=>implode(' ',$x['errors'])];
        $this->audit($r,'bulk_import','exam',$exam->id,'Excel import: '.$created.' new, '.$updated.' updated, '.count($errors).' not imported',['created'=>$created,'updated'=>$updated,'failed'=>count($errors)]);
        return response()->json(['success'=>true,'message'=>$errors?'Import finished: '.($created+$updated).' saved, '.count($errors).' row(s) NOT imported.':'Bulk import completed successfully.','total'=>count($a['rows']),'processed'=>$created+$updated,'created'=>$created,'updated'=>$updated,'failed'=>count($errors),'errors'=>$errors,'published'=>$publish]);
    }
    public function excelTemplate(Request $r) {
        $exam=$r->filled('exam_id')?$this->exam($r->query('exam_id')):null;$book=new Spreadsheet();$ws=$book->getActiveSheet();$headers=['Name','Roll','Registration','Group'];if($exam)foreach($this->subjectsFor($exam->class_name) as $s)$headers[]=$s->subject_code?:$s->subject_name;$ws->fromArray($headers,null,'A1');$ws->getStyle('1:1')->getFont()->setBold(true);$ws->freezePane('A2');$tmp=tempnam(sys_get_temp_dir(),'xlsx');(new Xlsx($book))->save($tmp);return response()->download($tmp,'result-template.xlsx')->deleteFileAfterSend(true);
    }

    // Export and backup
    public function exportCsv(Request $r) { if($x=$this->admin($r))return $x; $d=$this->report($r);$head=['Position','Roll','Name','Registration','Group'];foreach($d['subjects'] as $s)$head[]=$s['subject_name'].' ('.$s['subject_code'].')';array_push($head,'Total Marks','GPA','Grade','Result','Status');$rows=[$head];foreach($d['students'] as $s){$x=[$s['position']??'',$s['roll'],$s['name'],$s['registration'],$s['group_name']];foreach($d['subjects'] as $sub)$x[]=$s['marks'][$sub['id']]??'';array_push($x,$s['total_marks'],number_format($s['final_gpa'],2),$s['final_grade'],$s['result_status'],$s['status']);$rows[]=$x;}$csv="\\xEF\\xBB\\xBF";foreach($rows as $row)$csv.=implode(',',array_map(fn($v)=>'"'.str_replace('"','""',(string)$v).'"',$row))."\\r\\n";$this->audit($r,'export','exam',null,'Exported CSV');return response($csv,200,['Content-Type'=>'text/csv; charset=utf-8','Content-Disposition'=>'attachment; filename="results.csv"']);}
    public function exportXlsx(Request $r) { if($x=$this->admin($r))return $x; $d=$this->report($r);$book=new Spreadsheet();$ws=$book->getActiveSheet();$head=['Position','Roll','Name','Registration','Group'];foreach($d['subjects'] as $s)$head[]=$s['subject_name'].' ('.$s['subject_code'].')';array_push($head,'Total Marks','GPA','Grade','Result','Status');$ws->fromArray($head,null,'A1');foreach($d['students'] as $s){$x=[$s['position']??'',$s['roll'],$s['name'],$s['registration'],$s['group_name']];foreach($d['subjects'] as $sub)$x[]=$s['marks'][$sub['id']]??'';array_push($x,$s['total_marks'],$s['final_gpa'],$s['final_grade'],$s['result_status'],$s['status']);$ws->fromArray($x,null,'A'.($ws->getHighestRow()+1));}$ws->getStyle('1:1')->getFont()->setBold(true);$ws->freezePane('D2');$tmp=tempnam(sys_get_temp_dir(),'xlsx');(new Xlsx($book))->save($tmp);$this->audit($r,'export','exam',null,'Exported Excel');return response()->download($tmp,'results.xlsx')->deleteFileAfterSend(true);}
    public function backup(Request $r) {if($x=$this->admin($r))return $x;$data=['app'=>'school-result-system','version'=>2,'created_at'=>now()->toIso8601String()];foreach(['exams','subjects','students','results'] as $t)$data[$t]=DB::table($t)->orderBy('id')->get();$data['counts']=array_map(fn($t)=>count($data[$t]),['exams','subjects','students','results']);$this->audit($r,'backup','system',null,'Downloaded backup');return response()->json($data)->header('Content-Disposition','attachment; filename="result-backup-'.now()->format('Y-m-d').'.json"');}
    public function verifyResult($id,$sig) {
        $expected=hash_hmac('sha256',(string)$id,(string)(config('school.verify_secret') ?: config('app.key')));
        if(!ctype_digit((string)$id)||!hash_equals($expected,(string)$sig)) return response('<h2>Verification link is not valid.</h2>',404)->header('Content-Type','text/html');
        $s=DB::table('students')->where('id',$id)->where('status','published')->first();
        if(!$s) return response('<h2>This result is not available or has been withdrawn.</h2>',404)->header('Content-Type','text/html');
        $d=$this->resultData($s);
        return response()->view('verify-result',['data'=>$d],200)->header('X-Robots-Tag','noindex');
    }
    public function health() {try{DB::select('SELECT 1');return response()->json(['ok'=>true]);}catch(\\Throwable $e){return response()->json(['ok'=>false],503);}}
}
