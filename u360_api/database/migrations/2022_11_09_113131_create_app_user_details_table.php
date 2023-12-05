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
        Schema::create('app_user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('dob')->nullable();
            $table->string('location', 255)->nullable();
            $table->string('phone_code', 5)->nullable();
            $table->string('contact_number', 15)->nullable();
            // $table->enum('sponsor_type', ['1', '2'])->comment("1 : Individual,2 : Corporate");
            $table->string('corporation_name', 255)->nullable();
            $table->string('industry', 255)->nullable();
            $table->string('other_industry', 255)->nullable();
            $table->integer('city')->nullable();
            $table->integer('country')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->tinyInteger('twitter')->nullable()->comment("1=>Not at all, 2=>Rarely, 3=> regular");
            $table->tinyInteger('facebook')->nullable()->comment("1=>Not at all, 2=>Rarely, 3=> regular");
            $table->tinyInteger('linkedin')->nullable()->comment("1=>Not at all, 2=>Rarely, 3=> regular");
            $table->tinyInteger('instagram')->nullable()->comment("1=>Not at all, 2=>Rarely, 3=> regular");
            $table->tinyInteger('snapchat')->nullable()->comment("1=>Not at all, 2=>Rarely, 3=> regular");
            $table->tinyInteger('tiktok')->nullable();
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
        Schema::dropIfExists('app_user_details');
    }
};
