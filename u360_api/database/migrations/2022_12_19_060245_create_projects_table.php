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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('project_type', ['1', '2', '3'])
            ->default(1)->comment("1 : New,2 : Existing,3 : Emergency Relief");
            $table->string('project_donation_type', 20)->comment("1=>Donation,2=>Volunteer
            ")->nullable();
            $table->integer('category_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->string('default_image', 200)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->integer('volunteer')->nullable();
            $table->string('city', 50)->nullable();
            $table->unsignedBigInteger('country');
            $table->foreign('country')->references('id')->on('countries')->onDelete('cascade');
            $table->string('sdg_ids', 255)->nullable();
            $table->enum('status', ['Draft','Pending','Completed by Owner','Goal Reached','Approved','Rejected'])
            ->default('Draft');
            $table->enum('is_donation_reached', ['0','1'])->comment('0=>No,1=>Yes')->default('0');
            $table->string('stripe_product_id', 50)->nullable();
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
        Schema::dropIfExists('projects');
    }
};
