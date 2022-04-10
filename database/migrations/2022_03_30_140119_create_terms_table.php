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
        Schema::create('terms', function (Blueprint $table) {
            $table->id('term_id');
            $table->unsignedBigInteger('school_id');
            $table->string('name', 25);
            $table->year('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('school_id')
                ->references('school_id')
                ->on('schools')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
};
