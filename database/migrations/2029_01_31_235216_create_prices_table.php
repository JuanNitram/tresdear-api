<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
            Schema::create('prices', function (Blueprint $table) {
                $table->increments('id');

                $table->string('title');
                $table->string('subtitle');
                $table->string('details');
                $table->string('description');
                $table->string('price');

                $table->boolean('iva')->default(0);
                $table->boolean('active')->default(1);
                $table->float('pos')->default(0);

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
         Schema::dropIfExists('prices');
    }
}
