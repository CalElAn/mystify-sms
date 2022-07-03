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
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('term_id');
            $table->string('class_name', 25);
            $table->string('class_suffix', 25);
            $table->string('subject_name', 50);
            $table->decimal('class_mark', 4)->nullable();
            $table->decimal('exam_mark', 4)->nullable();
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
                ->onDelete('cascade');
            
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
            
            $table->foreign('term_id')
                ->references('id')
                ->on('terms')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign(['school_id', 'class_name', 'class_suffix'])
                ->references(['school_id', 'name', 'suffix'])
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('subject_name')
                ->references('name')
                ->on('subjects')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['student_id', 'term_id', 'class_name', 'class_suffix', 'subject_name'], 'grades_unique');
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
