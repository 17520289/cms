<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalInfoToEmployeeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table -> string('permanent_address')->after('designation_id')->nullable() ; // địa chỉ thường chú
            $table -> string('tempcreorary_address')->after('permanent_address')->nullable(); // địa chỉ tạm chú
            $table -> string('id_no') ->after('temporary_address') ->nullable(); //số chứng minh
            $table -> string('issue_date') ->after('id_no') ->nullable();
            $table -> string('place_of_issue') ->after('issue_date') ->nullable();
            $table->double('prob_salary')->nullable()->after('place_of_issue');
            $table->double('office_salary')->nullable()->after('prob_salary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn('hourly_rate');
            $table->dropColumn('address');
        });
    }
}
