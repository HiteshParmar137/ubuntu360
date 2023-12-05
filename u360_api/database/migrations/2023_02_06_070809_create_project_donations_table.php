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
        Schema::create('project_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('recurring_donation_id')->nullable();
            $table->text('comment')->nullable();
            $table->decimal('donation_amount', 10, 2)->nullable();
            $table->enum('donation_type', ['1', '2'])->comment("1=>One Time, 2=>Recurring");
            $table->date('month_end_date')->nullable();
            $table->decimal('tips_amount', 10, 2)->nullable();
            $table->enum('is_recurring_start', ['0', '1'])->comment("0=>No,1=>Yes");
            $table->string('email', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('document', 255)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->enum('transaction_type', ['1','2'])->comment("1=>normal,2=>subscription");
            $table->string('customer_id', 255)->nullable();
            $table->enum('status', ['Paid', 'Unpaid']);
            $table->enum('is_recurring_stop', ['0','1'])->comment("0=>No,1=>Yes");
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
        Schema::dropIfExists('project_donations');
    }
};
