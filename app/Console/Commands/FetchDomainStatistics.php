<?php

namespace App\Console\Commands;

use App\Jobs\FetchDomainStatisticsJob;
use Illuminate\Console\Command;

class FetchDomainStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-domain-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch domain statistics from API and save to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        FetchDomainStatisticsJob::dispatch();
        $this->info('Domain statistics fetch job dispatched.');
    }
}
