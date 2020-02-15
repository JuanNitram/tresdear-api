<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
            Schema::create('clients', function (Blueprint $table) {
                $table->increments('id');

                $table->text('name')->nullable();

                $table->float('pos')->default(0);
                $table->boolean('active')->default(1);

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
         Schema::dropIfExists('clients');
    }
}
