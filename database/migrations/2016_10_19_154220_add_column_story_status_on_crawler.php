<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStoryStatusOnCrawler extends Migration
{
    public function up()
    {
        Schema::table('crawlers', function($table) {
          $table->string('status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('crawlers', function($table) {
          $table->dropColumn('status');
        });
    }
}
