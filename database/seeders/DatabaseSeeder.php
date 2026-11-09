<?php
namespace Database\\Seeders;
use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Hash;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  $username=trim((string)env('ADMIN_USERNAME','')); $password=(string)env('ADMIN_PASSWORD','');
  if($username===''||strlen($password)<8){$this->command?->warn('Set ADMIN_USERNAME and ADMIN_PASSWORD (8+ characters), then run php artisan db:seed.');return;}
  if(DB::table('admins')->exists())return;
  DB::table('admins')->insert(['username'=>$username,'password'=>Hash::make($password),'role'=>'admin','active'=>true,'must_change_password'=>false,'created_at'=>now(),'updated_at'=>now()]);
  $this->command?->info('Initial administrator created. Remove ADMIN_PASSWORD from environment after setup.');
 }
}
