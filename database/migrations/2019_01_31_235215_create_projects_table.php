<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
            Schema::create('projects', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name');
                $table->text('description')->nullable();
                $table->text('description_quill')->nullable();
                $table->text('link')->nullable();

                $table->boolean('highlighted')->default(0);
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
         Schema::dropIfExists('projects');
    }
}
