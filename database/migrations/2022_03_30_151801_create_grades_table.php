<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('term_id');
            $table->string('subject_name', 50);
            $table->decimal('mark', 3);
            $table->timestamps();

            $table->foreign('school_id')
                ->references('school_id')
                ->on('schools')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            
            $table->foreign('term_id')
                ->references('term_id')
                ->on('terms')
                ->onUpdate('cascade')
                ->onDelete('no action');

            $table->unique(['school_id', 'student_id', 'teacher_id', 'term_id', 'subject_name'], 'grades_unique');
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
    }
};
