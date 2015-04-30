<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConvosMessagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convos_messages', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('conversation_id')->unsigned();

            $table->text('body');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('conversation_id')
                ->references('id')->on('convos_conversations');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public
    function down()
    {
        Schema::drop('convos_messages');
    }

}
