<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Jobs\StoreCategoryJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CategoryJobController extends Controller
{
    /**
     * stock job for storing categories.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stockCategories(Request $request)
    {
        StoreCategoryJob::dispatch($request->all());
        return to_route('categories.index');
    }

    /**
     * Store categories from cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCategoriesByQueue()
    {
        Log::info('Start: storeCategories');
        Artisan::call('queue:work --stop-when-empty', []);
        Log::info('Success: stored categories by queue');
        return to_route('categories.index');
    }
}
