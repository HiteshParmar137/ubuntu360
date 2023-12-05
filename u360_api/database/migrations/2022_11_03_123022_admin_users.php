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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['1', '2'])->comment("1 : Administrator,2 : AppUser");
            $table->string('name', 255)->nullable();
            $table->string('email', 55)->nullable();
            $table->string('password', 255)->nullable();
            $table->unsignedBigInteger('user_group_id');
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
            $table->enum('status', ['1', '0'])->comment("1 : Active,0 : InActive");
            $table->string('image', 255)->nullable();
            $table->string('reset_password_token', 255)->nullable();
            $table->longText('api_token')->nullable();
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
};
