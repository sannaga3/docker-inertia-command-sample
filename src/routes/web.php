<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Job\CategoryJobController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/tasks', TaskController::class)->except(['show', 'create', 'edit']);
    Route::resource('/categories', CategoryController::class)->except(['show', 'create', 'edit']);

    Route::prefix('categories')->group(function () {
        Route::post('/keep_cache', [CategoryController::class, 'keepCache'])->name('categories.keep_cache');
        Route::get('/cache_list', [CategoryController::class, 'getCache'])->name('categories.cache_list');
        Route::post('/insert_cache', [CategoryController::class, 'insertCategoriesFromCache'])->name('categories.insert_cache');
        Route::post('/clear_cache', [CategoryController::class, 'clearCache'])->name('categories.clear_cache');
        Route::post('/stock_queue', [CategoryJobController::class, 'stockCategories'])->name('categories.stock_queue');
        Route::post('/work_queue', [CategoryJobController::class, 'storeCategoriesByQueue'])->name('categories.work_queue');
    });
});

require __DIR__ . '/auth.php';
