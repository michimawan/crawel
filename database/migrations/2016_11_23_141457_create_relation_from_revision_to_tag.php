<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationFromRevisionToTag extends Migration
{
    public function up()
    {
        Schema::table('tags', function($table) {
          $table->integer('revision_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tags', function($table) {
          $table->dropColumn('revision_id');
        });
    }
}
