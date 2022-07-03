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
        Schema::create('school_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->decimal('amount', 8, 4);
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('academic_year_id')
                ->references('id')
                ->on('academic_years')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['student_id', 'school_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_fees');
    }
};
