<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('transaction_history_id');
            $table->smallInteger('transaction_type');
            $table->unsignedDecimal('amount', 16, 2)->default(0);
            $table->unsignedDecimal('balance_before', 16, 2)->default(0);
            $table->unsignedDecimal('balance_after', 16, 2)->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('balance_transaction_histories');
    }
}
