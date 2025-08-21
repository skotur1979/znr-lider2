<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $godina = $request->input('godina', now()->year);
        return Excel::download(new ExpensesExport($godina), 'Troskovi_' . $godina . '.xlsx');
    }
}
