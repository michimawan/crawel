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
            $table->integer('project_id');
            $table->text('story_type')->nullable();
            $table->text('last_updated_at');
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
