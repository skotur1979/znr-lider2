<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Livewire\AvailableTests;
use App\Http\Livewire\TestForm;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestAttemptController;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;

// Prikaz dostupnih testova i rješavanje
Route::middleware(['auth'])->group(function () {
    Route::get('/testovi', AvailableTests::class)->name('testovi');
    Route::get('/test/{test}', TestForm::class)->name('testovi.pokreni');
    Route::get('/test-attempts/{attempt}', [TestAttemptController::class, 'show'])->name('test-attempts.show');
    

    Route::get('/test-attempts/{attempt}/pdf', [TestAttemptController::class, 'downloadPdf'])->name('test-attempts.download');
    Route::middleware(['auth'])->group(function () {
    Route::get('/tests/{test}/start', TestForm::class)->name('tests.start'); // alias
});

    
});

Route::post('/potpis/upload', function (Request $request) {
    $image = base64_decode($request->input('image'));
    $fileName = 'signature_' . time() . '.png';
    $path = 'signatures/' . $fileName;

    Storage::disk('public')->put($path, $image);

    return response()->json(['path' => $path]);
});

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