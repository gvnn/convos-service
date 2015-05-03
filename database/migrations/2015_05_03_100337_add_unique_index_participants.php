<?php

use Illuminate\Database\Migrations\Migration;

class AddUniqueIndexParticipants extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('convos_participants', function ($table) {
            $table->unique(['user_id', 'conversation_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('convos_participants', function ($table) {

            $table->dropForeign('convos_participants_conversation_id_foreign');
            $table->dropForeign('convos_participants_user_id_foreign');

            $table->dropIndex('convos_participants_user_id_conversation_id_unique');

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('conversation_id')
                ->references('id')->on('convos_conversations');
        });
    }

}
