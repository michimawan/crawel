<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRevisions extends Migration
{
    public function up()
    {
        Schema::create('revisions', function(Blueprint $table) {
            $table->increments('id');
            $table->string('child_tag_revisions');
            $table->string('end_time_check_story')->default('-');
            $table->string('end_time_run_automate_test')->default('-');
            $table->string('time_get_canary')->default('-');
            $table->string('time_to_elb')->default('-');
            $table->text('description');
            $table->string('project');
            $table->timestamps();

            $table->unique(['child_tag_revisions']);
        });
    }

    public function down()
    {
        Schema::drop('revisions');
    }
}
