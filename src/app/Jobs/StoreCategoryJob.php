<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Log;

class StoreCategoryJob implements ShouldQueue
{
  protected $category;

  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $data;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * Execute the job.
   * 
   * @return void
   */
  public function handle()
  {
    Log::info('Start: storeCategoryJob');
    $categoryService = new CategoryService();
    $categoryService->storeCategories($this->data);
    Log::info('Success: stocked categories by queue');
  }
}
