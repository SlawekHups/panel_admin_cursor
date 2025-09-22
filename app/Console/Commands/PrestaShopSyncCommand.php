<?php

namespace App\Console\Commands;

use App\Jobs\PullPrestaShopOrdersJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PrestaShopSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prestashop:sync {--since=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync orders, customers, and products from PrestaShop';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $since = $this->option('since') ? Carbon::parse($this->option('since')) : null;

        $this->info('Starting PrestaShop synchronization...');

        if ($since) {
            $this->info("Syncing data since: {$since->format('Y-m-d H:i:s')}");
        } else {
            $this->info('Syncing all data...');
        }

        // Dispatch sync jobs
        PullPrestaShopOrdersJob::dispatch($since);

        $this->info('Synchronization jobs dispatched successfully!');
    }
}
