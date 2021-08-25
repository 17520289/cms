<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table -> increments('id');
            $table -> integer('user_id')->unsigned();
            $table -> foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table -> string('account_owner')->nullable();
            $table -> string('account_number')->nullable();
            $table -> string('bank_name')->nullable();
            $table -> string('branch')->nullable();
            $table -> timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            //
        });
    }
}
