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
        Schema::create('class_teacher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id')->index();
            $table->unsignedBigInteger('academic_year_id');
            $table->timestamps();

            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            //one class should have only one class teacher for each academic year
            // $table->unique(['class_id', 'academic_year_id']);
            //one teacher should have only one class for each academic year
            // $table->unique(['teacher_id', 'academic_year_id']);

            //currently the unique indexes are commented out because creating form validation for them would be a pain 
            //currently only the fisrt class teacher is displayed when a class is showed so it shouldnt be a problem 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_teacher');     
    }
};
