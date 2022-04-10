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
        Schema::create('class_student_pivot', function (Blueprint $table) {
            $table->id();
            $table->string('class_name', 25);
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('term_id');
            $table->timestamps();

            $table->foreign('class_name')
                ->references('class_name')
                ->on('classes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('term_id')
                ->references('term_id')
                ->on('terms')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['class_name', 'student_id', 'term_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_user_pivot');
    }
};
