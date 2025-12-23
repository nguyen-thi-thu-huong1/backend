<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFsLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fs_levels', function (Blueprint $table) {
            $table->integer('type')->default(1)->comment('类型')->change();
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
            $table->unsignedTinyInteger('type')->default(1)->comment('类型')->change();
        });
    }
}
