<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagForStories extends Migration
{
    public function up()
    {
        Schema::create('tags', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('timing');
            $table->string('project');
            $table->timestamps();

            $table->unique(['code', 'project']);
        });
    }

    public function down()
    {
        Schema::drop('tags');
    }
}
