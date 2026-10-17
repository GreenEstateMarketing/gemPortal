<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillDatePublishedForApprovedProperties extends Migration
{
    public function up()
    {
        DB::table('re_properties')
            ->where('moderation_status', 'approved')
            ->whereNull('date_published')
            ->update(['date_published' => now()]);
    }

    public function down()
    {
        DB::table('re_properties')
            ->where('moderation_status', 'approved')
            ->update(['date_published' => null]);
    }
}
