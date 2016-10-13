<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnLastUpdatedAt extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE crawlers MODIFY last_updated_at json');
    }

    public function down()
    {
        DB::statement('ALTER TABLE crawlers MODIFY last_updated_at text');
    }
}
