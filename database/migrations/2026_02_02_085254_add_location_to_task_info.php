<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('task_info', function (Blueprint $table) {
        // Adding location after department ID
        $table->string('location')->nullable()->after('dept_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('task_info', function (Blueprint $table) {
        $table->dropColumn('location');
    });
}
};
