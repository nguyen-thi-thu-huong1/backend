<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToFsLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fs_levels', function (Blueprint $table) {
            $table->integer('game_type')->nullable()->change();
            $table->integer('product_type')->nullable()->after('game_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fs_levels', function (Blueprint $table) {
            $table->integer('game_type')->default(1)->comment('游戏类型')->change();
            $table->dropColumn('product_type');
        });
    }
}
