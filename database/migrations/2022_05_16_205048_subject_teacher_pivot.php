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
        Schema::create('subject_teacher_pivot', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name', 50);
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('term_id');
            $table->timestamps();

            $table->foreign('subject_name')
                ->references('name')
                ->on('subjects')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('school_id')
                ->references('school_id')
                ->on('schools')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('class_id')
                ->references('class_id')
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('term_id')
                ->references('term_id')
                ->on('terms')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            //teacher_id added in case of scenario where more than one teacher can add marks
            $table->unique(['teacher_id', 'term_id', 'class_id', 'subject_name'], 'subject_teacher_pivot_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_teacher_pivot');
    }
};
