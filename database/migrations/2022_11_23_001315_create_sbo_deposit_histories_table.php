<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSboDepositHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sbo_deposit_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->string('txnId');
            $table->string('refno')->nullable();
            $table->integer('balance');
            $table->string('outstanding')->nullable();
            $table->string('serverId')->nullable();
            $table->string('status', 4);
            $table->dateTime('deposit_at')->nullable();
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
        Schema::dropIfExists('sbo_deposit_histories');
    }
}
