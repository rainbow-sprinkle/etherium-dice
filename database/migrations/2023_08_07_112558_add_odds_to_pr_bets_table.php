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
    Schema::table('pr_bets', function (Blueprint $table) {
        $table->float('odds')->after('selected_odd');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('pr_bets', function (Blueprint $table) {
        $table->dropColumn('odds');
    });
}
};
