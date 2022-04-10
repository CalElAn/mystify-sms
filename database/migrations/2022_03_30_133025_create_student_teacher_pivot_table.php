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
        Schema::create('student_teacher_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('class_name', 25);
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            
            $table->foreign('class_name')
                ->references('class_name')
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['student_id', 'teacher_id', 'class_name']);
        });

        // if (config('app.env') !== 'testing') {
        //     DB::statement('ALTER TABLE student_teacher_pivot ADD CONSTRAINT CHECK (student_id <> teacher_id);');
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_teacher_pivot');
    }
};
