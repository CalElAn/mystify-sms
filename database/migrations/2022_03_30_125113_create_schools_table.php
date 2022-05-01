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
        Schema::create('schools', function (Blueprint $table) {
            $table->id('school_id');
            $table->string('name');
            $table->string('city');
            $table->string('town');
            $table->string('street_address');
            $table->unsignedBigInteger('grading_scale_id');
            $table->decimal('class_mark_percentage')->default(0.30);
            $table->decimal('exam_mark_percentage')->default(0.70);
            $table->timestamps();

            $table->foreign('grading_scale_id')
                ->references('grading_scale_id')
                ->on('grading_scales')
                ->onUpdate('cascade')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools');
    }
};
