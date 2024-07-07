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
    Schema::table('betslips', function (Blueprint $table) {
        $table->timestamp('freeze_time')->nullable(); // add this line
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('betslips', function (Blueprint $table) {
        $table->dropColumn('freeze_time'); // add this line
    });
}
};
