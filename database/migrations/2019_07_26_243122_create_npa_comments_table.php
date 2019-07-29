<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateNpaCommentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'npa_comments',
            function (Blueprint $table) {
                $table->increments('id');

                $table->integer('npa_id')->nullable()->comment('Ид документа');
                $table->integer('comment_id')->nullable()->comment('Ид родительского документа');
                $table->boolean('moderated')->default(false)->comment('Промодерирован');

                $langs = Langs::all();

                foreach ($langs as $lang) {
                    $table->mediumText('comment_' . $lang->key)->nullable();
                }

                $table->integer('created_user')->nullable();
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
        Schema::dropIfExists('npa_comments');
    }
}
