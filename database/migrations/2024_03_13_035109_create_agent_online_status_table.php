<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentOnlineStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_online_status', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('agent_id');
            $table->timestamp('latest_online_at')->nullable();
            $table->timestamp('loggedin_at')->nullable();
            $table->timestamp('loggedout_at')->nullable();
            $table->string('device_id',10)->default('PC')->comment('device being used');

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
        Schema::dropIfExists('agent_online_status');
    }
}
