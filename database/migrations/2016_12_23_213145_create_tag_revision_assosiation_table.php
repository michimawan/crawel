<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagRevisionAssosiationTable extends Migration
{
    public function up()
    {
        Schema::create('tag_revision', function(Blueprint $table) {
            $table->integer('tag_id');
            $table->integer('revision_id');
        });
    }

    public function down()
    {
        Schema::drop('tag_revision');
    }
}
