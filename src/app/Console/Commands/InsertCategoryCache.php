<?php

namespace App\Console\Commands;

use App\Http\Controllers\CategoryController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertCategoryCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insertCategoryCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert the cache of categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        resolve(CategoryController::class)->insertCache();
        Log::info("The command insertCategoryCache was succeeded.");

        return Command::SUCCESS;
    }
}
