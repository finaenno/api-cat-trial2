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
        Schema::create('cats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('breed')->nullable();
            $table->string('gender');
            $table->string('color');
            $table->string('eye_color');
            $table->string('hair_color');
            $table->string('ear_shape');
            $table->double('weight',5,2);
            $table->integer('age');
            $table->string('photo');
            $table->double('lat', 15, 10)->nullable();
            $table->double('lon', 15, 10)->nullable();
            $table->integer('isWhite');
            $table->text('story');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cats');
    }
};
