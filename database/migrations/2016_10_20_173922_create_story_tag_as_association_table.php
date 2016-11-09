<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoryTagAsAssociationTable extends Migration
{
    public function up()
    {
        Schema::create('story_tag', function(Blueprint $table) {
            $table->integer('story_id');
            $table->integer('tag_id');
        });
    }

    public function down()
    {
        Schema::drop('story_tag');
    }
}
