<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConvosParticipantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('convos_participants', function (Blueprint $table) {

            $table->increments('id');

            $table->boolean('is_creator');
            $table->boolean('is_read');

            $table->timestamp('read_at');

            $table->integer('user_id')->unsigned();
            $table->integer('conversation_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('conversation_id')
                ->references('id')->on('convos_conversations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('convos_participants');
	}

}
