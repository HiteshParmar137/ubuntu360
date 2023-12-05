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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->enum('user_type', ['1', '2'])->comment("1 : Admin,2 : App user");
            $table->enum('user_type', ['1', '2'])->comment("1 : Individual,2 : Corporate")->nullable();
            $table->string('name',50)->nullable();
            $table->string('email',100)->unique()->nullable();
            $table->string('image',255)->nullable();
            $table->enum('status', ['0', '1'])->default(0)->comment("1 : Active,0 : InActive");
            $table->string('password',255);
            $table->text('about')->nullable();
            $table->string('reset_password_token', 255)->nullable();
            $table->string('verify_email_token', 255)->nullable();
            $table->string('social_id', 100);
            $table->tinyInteger('is_email_verified')->default(0)->comment("1 : Yes,0 : No");
            $table->string('email_verify_token', 255)->nullable();
            $table->tinyInteger('is_signup_completed')->default(0)->comment("1 : Yes,0 : No");
            $table->longText('api_token')->nullable();
            $table->timestamp('last_logged_in')->nullable();
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
        Schema::dropIfExists('users');
    }
};
