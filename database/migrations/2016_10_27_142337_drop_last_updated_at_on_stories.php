<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLastUpdatedAtOnStories extends Migration
{
    public function up()
    {
        Schema::table('stories', function($table) {
          $table->dropColumn('last_updated_at');
        });
    }

    public function down()
    {
        Schema::table('stories', function($table) {
          $table->json('last_updated_at');
        });
    }
}
