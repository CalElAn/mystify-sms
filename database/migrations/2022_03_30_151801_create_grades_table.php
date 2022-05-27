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
            $table->string('class_name', 25);
            $table->string('class_suffix', 25);
            $table->string('subject_name', 50);
            $table->decimal('class_mark', 4);
            $table->decimal('exam_mark', 4);
            $table->timestamps();

            $table->foreign('school_id')
                ->references('id')
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
                ->references('id')
                ->on('terms')
                ->onUpdate('cascade')
                ->onDelete('no action');
            
            $table->foreign(['school_id', 'class_name', 'class_suffix'])
                ->references(['school_id', 'name', 'suffix'])
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('subject_name')
                ->references('name')
                ->on('subjects')
                ->onUpdate('cascade')
                ->onDelete('no action');

            $table->unique(['student_id', 'term_id', 'class_name', 'subject_name'], 'grades_unique');
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
