<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use Barryvdh\DomPDF\Facade\Pdf;

class TestAttemptController extends Controller
{
    public function show(TestAttempt $attempt)
    {
        // Učitaj povezane podatke (pitanja i odgovori)
        $attempt->load([
            'test.questions.answers',
            'odgovori' // korisnički odabrani odgovori
        ]);

        return view('test-result.show', compact('attempt'));
    }
    public function index()
{
    $attempts = \App\Models\TestAttempt::with('test')->latest()->get();

    return view('test-result.index', compact('attempts'));
}
public function downloadPdf(TestAttempt $attempt)
{
    $attempt->load(['test.questions.answers', 'odgovori']);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('test-result.pdf', compact('attempt'));

    return $pdf->download('rijeseni-test.pdf');
}
}
