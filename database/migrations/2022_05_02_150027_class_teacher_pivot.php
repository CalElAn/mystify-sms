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
        Schema::create('class_teacher_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('teacher_id')->index();
            $table->unsignedBigInteger('academic_year_id');
            $table->timestamps();

            $table->foreign('class_id')
                ->references('class_id')
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('academic_year_id')
                ->references('academic_year_id')
                ->on('academic_years')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            //one class should have only one class teacher for each academic year
            $table->unique(['class_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_teacher_pivot');     
    }
};
