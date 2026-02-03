<?php

namespace App\Console\Commands;

use App\Jobs\PublishOutboxMessages;
use Illuminate\Console\Command;

class PublishOutboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outbox:publish {--batch=100 : Batch size for processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish unpublished outbox messages to queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = (int) $this->option('batch');
        
        $this->info("Publishing outbox messages (batch size: {$batchSize})...");
        
        PublishOutboxMessages::dispatch($batchSize);
        
        $this->info('Outbox publish job dispatched successfully!');
        
        return Command::SUCCESS;
    }
}
