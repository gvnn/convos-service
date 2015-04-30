<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConvosConversationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convos_conversations', function (Blueprint $table) {

            $table->increments('id');
            $table->string('subject', 140);

            $table->integer('created_by')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('convos_conversations');
    }

}
