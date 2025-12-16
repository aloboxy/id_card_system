<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools');
            $table->string('student_id'); // Removed unique constraint here, will add composite unique later
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->default('Nigerian');
            $table->string('phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('Nigeria');
            
            // Academic Information
            $table->string('class');
            $table->string('section')->nullable();
            $table->string('roll_number')->nullable();
            $table->string('admission_number');
            $table->unique(['school_id', 'student_id']);
            $table->unique(['school_id', 'admission_number']);
            $table->date('admission_date');
            
            // Parent/Guardian Information
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->text('guardian_address')->nullable();
            
            // ID Card Related
            $table->foreignId('id_card_template_id')->nullable()->constrained('id_card_templates');
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->date('id_card_issue_date')->nullable();
            $table->date('id_card_expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
