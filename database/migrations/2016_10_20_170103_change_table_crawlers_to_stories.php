<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableCrawlersToStories extends Migration
{
    public function up()
    {
        Schema::rename('crawlers', 'stories');
    }

    public function down()
    {
        Schema::rename('stories', 'crawlers');
    }
}
