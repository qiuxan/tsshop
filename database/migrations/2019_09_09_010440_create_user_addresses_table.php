<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //this is to tell the database when deleting the user, the related addresses will be deleted as well
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('post_code');
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->dateTime('last_used_at')->nullable();
            // last_used_at can be a NULL data
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
        Schema::dropIfExists('user_addresses');
    }
}
