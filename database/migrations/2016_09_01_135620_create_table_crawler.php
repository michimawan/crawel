<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCrawler extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawlers', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('pivotal_id');
            $table->text('title')->nullable();
            $table->integer('point')->default(0);
            $table->text('project_name')->nullable();
            $table->text('story_type')->nullable();
            $table->timestamps();

            $table->unique('pivotal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('crawlers');
    }
}
