<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher'])->default('admin');
            $table->boolean('active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name', 50)->unique();
            $table->timestamps();
        });
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name', 150);
            $table->string('subject_code', 30);
            $table->unsignedInteger('full_marks');
            $table->enum('subject_type', ['main', 'fourth'])->default('main');
            $table->string('class_name', 50)->nullable()->index();
            $table->timestamps();
            $table->unique(['class_name', 'subject_code']);
        });
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_name', 150);
            $table->unsignedSmallInteger('exam_year');
            $table->string('class_name', 50);
            $table->timestamps();
            $table->unique(['exam_name', 'exam_year', 'class_name']);
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('roll', 50);
            $table->string('registration', 100)->nullable();
            $table->string('class_name', 50);
            $table->string('group_name', 50)->nullable();
            $table->string('exam_name', 150);
            $table->unsignedSmallInteger('exam_year');
            $table->string('institute_name')->nullable();
            $table->string('eiin', 30)->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->decimal('final_gpa', 4, 2)->default(0);
            $table->string('final_grade', 3)->default('F');
            $table->string('result_status', 10)->default('Fail');
            $table->timestamps();
            $table->unique(['class_name', 'exam_name', 'exam_year', 'roll'], 'uq_students_roll');
            $table->index(['class_name', 'exam_name', 'exam_year', 'status']);
        });
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->decimal('marks', 8, 2)->default(0);
            $table->string('grade', 3)->nullable();
            $table->decimal('grade_point', 3, 2)->default(0);
            $table->boolean('is_fourth_subject')->default(false);
            $table->timestamps();
            $table->unique(['student_id', 'subject_id']);
        });
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('username')->nullable();
            $table->string('role', 20)->nullable();
            $table->string('action', 80);
            $table->string('entity', 80)->nullable();
            $table->string('entity_id', 80)->nullable();
            $table->text('summary')->nullable();
            $table->json('details')->nullable();
            $table->string('ip', 45)->nullable();
            $table->index(['created_at']);
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        foreach (['sessions','audit_log','results','students','exams','subjects','classes','admins'] as $table) Schema::dropIfExists($table);
    }
};
