<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoColumnToTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_histories', function (Blueprint $table) {
            $table->unsignedDecimal('balance_before', 16, 2)->default(0)->after('win_loss');
            $table->unsignedDecimal('balance_after', 16, 2)->default(0)->after('balance_before');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_histories', function (Blueprint $table) {
            $table->dropColumn('balance_before');
            $table->dropColumn('balance_after');
        });
    }
}
