<?php

use App\Models\Langs;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMainFileColToNpaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('npa', function (Blueprint $table) {
            $table->dropColumn('mainFile');

            $langs = Langs::all();

            foreach ($langs as $lang) {
                $table->integer('mainFile_' . $lang->key)->nullable();
            }
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

            foreach ($langs as $lang) {
                $table->dropColumn('mainFile_' . $lang->key);
            }
        });
    }
}
