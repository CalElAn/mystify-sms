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
        Schema::create('class_suffixes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('class_name', 25);
            $table->string('suffix');
            $table->timestamps();

            $table->foreign('school_id')
                ->references('school_id')
                ->on('schools')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('class_name')
                ->references('class_name')
                ->on('classes')
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
        Schema::dropIfExists('class_suffixes');
    }
};
