<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummarisedLoginAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summarised_login_attempts', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('all', 16);
	        $table->string('ip_address', 16);
	        $table->string('ip_16', 16);
	        $table->string('ip_24', 16);
	        $table->string('username', 255);
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
        Schema::dropIfExists('summarised_login_attempts');
    }
}
