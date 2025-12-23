<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('member_id');
            $table->date('date');
            $table->decimal('balance_start_day', 16, 2)->default(0);
            $table->decimal('balance_middle_day', 16, 2)->default(0);
            $table->decimal('bet_amount', 16, 2)->default(0);
            $table->decimal('canceled_amount', 16, 2)->default(0);
            $table->decimal('pending_amount', 16, 2)->default(0);
            $table->decimal('win_loss', 16, 2)->default(0);
            $table->decimal('commission', 16, 2)->default(0);
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
        Schema::dropIfExists('account_balances');
    }
}
