<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CloseProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gem:close-properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command closes properties that have been published for a month';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $properties = DB::table('re_properties')
            ->where('moderation_status', '=', 'approved')
            ->where('date_published', '<=', Carbon::now()->subDays(30));

        $total = count($properties->get());

        $properties->update(['moderation_status' => 'closed']);

        $this->info($total . ' properties have been closed.');
    }
}
