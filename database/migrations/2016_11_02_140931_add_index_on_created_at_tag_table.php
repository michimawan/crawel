<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexOnCreatedAtTagTable extends Migration
{
    public function up()
    {
        Schema::table('tags', function($table) {
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::table('tags', function($table) {
            $table->dropIndex(['created_at']);
        });
    }
}
