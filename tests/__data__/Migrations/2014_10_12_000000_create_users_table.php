<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\User;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create(User::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string(User::COL_NAME);
            $table->string(User::COL_EMAIL)->unique();
            $table->timestamp(User::COL_EMAIL_VERIFIED_AT)->nullable();
            $table->string(User::COL_PASSWORD);
            $table->rememberToken();
            $table->string(User::COL_CONTRIBUTION_GROUP)->nullable();
            $table->string(User::COL_PAYMENT_INTERVAL)->nullable();
            $table->timestamp(User::COL_JOIN_DATE)->nullable();
            $table->timestamp(User::COL_EXIT_DATE)->nullable();
            $table->integer(User::COL_COUNT_SHARES)->unsigned()->nullable();
            $table->foreignId(User::COL_CURRENT_TEAM_ID)->nullable();
            $table->string(User::COL_PROFILE_PHOTO_PATH, 2048)->nullable();

            $table->timestamp(User::COL_CREATED_AT)->nullable();
            $table->timestamp(User::COL_UPDATED_AT)->nullable();
        });
    }
}
