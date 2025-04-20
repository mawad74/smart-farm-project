<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf; // استخدم الـ Facade مباشرة
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // عرض قائمة التقارير
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // الـ Admin يشوف كل التقارير
            $reports = Report::with('farm', 'user')->get();
        } else {
            // الـ Farmer Manager يشوف تقاريره بس
            $reports = Report::with('farm', 'user')
                ->where('user_id', $user->id)
                ->get();
        }

        return view('reports.index', compact('reports'));
    }

    // عرض تقرير معين
    public function show($id)
    {
        $user = Auth::user();
        
        $report = Report::with('reportDetails', 'farm', 'user')->findOrFail($id);

        // التأكد إن الـ Farmer Manager يشوف تقاريره بس
        if ($user->role !== 'admin' && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        return view('reports.show', compact('report'));
    }

    // تصدير التقرير بصيغة PDF
    public function exportToPDF($id)
    {
        $user = Auth::user();
        
        $report = Report::with('reportDetails', 'farm', 'user')->findOrFail($id);

        // التأكد إن الـ Farmer Manager يشوف تقاريره بس
        if ($user->role !== 'admin' && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $pdf = Pdf::loadView('reports.pdf', compact('report')); // استخدم Pdf بدل PDF
        return $pdf->download('report_' . $id . '.pdf');
    }
}

