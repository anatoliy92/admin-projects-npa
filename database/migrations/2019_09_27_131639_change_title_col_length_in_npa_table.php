<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class ChangeTitleColLengthInNpaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('npa', function (Blueprint $table) {
            $langs = Langs::all();

            foreach ($langs as $lang) { $table->text('title_' . $lang->key)->change(); }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('npa', function (Blueprint $table) {
            $langs = Langs::all();

            foreach ($langs as $lang) { $table->string('title_' . $lang->key)->change(); }
        });
    }
}
