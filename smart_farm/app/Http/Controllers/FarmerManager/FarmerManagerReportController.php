<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Farm;
use App\Models\ReportRequest;
use App\User; 
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class FarmerManagerReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager'); // Middleware للـ Farmer Manager
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // جلب كل مزارع المستخدم
        $farms = $user->farms()->pluck('id');
        // جلب أول مزرعة فقط للعرض في العنوان (يمكنك تعديله لاحقًا)
        $farm = $user->farms()->first();

        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $type = $request->input('type');

        // جلب كل تقارير كل مزارع المستخدم
        $reportsQuery = Report::whereIn('farm_id', $farms)->with('user');

        // البحث
        if ($search) {
            $reportsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }

        // فلترة حسب النوع
        if ($type) {
            $reportsQuery->where('type', $type);
        }

        // الترتيب
        if ($sort === 'newest') {
            $reportsQuery->latest();
        } elseif ($sort === 'oldest') {
            $reportsQuery->oldest();
        }

        $reports = $reportsQuery->paginate(10);
        $pendingRequests = ReportRequest::where('user_id', $user->id)->where('status', 'pending')->get();

        return view('farmer_manager.reports.index', compact('reports', 'farm', 'pendingRequests'));
    }

    public function show($id)
    {
        $user = Auth::user();
        // جلب كل مزارع المستخدم
        $farms = $user->farms()->pluck('id');
        // البحث عن التقرير في كل مزارع المستخدم
        $report = Report::whereIn('farm_id', $farms)->with('reportDetails')->findOrFail($id);

        return view('farmer_manager.reports.show', compact('report'));
    }

    public function storeRequest(Request $request)
    {
        $user = Auth::user();
        try {
            $validated = $request->validate([
                'type' => 'required|in:crop_health,resource_usage,environmental_conditions,alert_history,system_performance',
                'farm_id' => 'required|exists:farms,id',
            ]);

            $reportRequest = ReportRequest::create([
                'user_id' => $user->id,
                'farm_id' => $request->farm_id,
                'type' => $request->type,
                'status' => 'pending',
            ]);

            Log::info('Report request created', ['request_id' => $reportRequest->id, 'user_id' => $user->id]);

            return redirect()->route('farmer_manager.reports.index')->with('success', 'The request has been successfully submitted and is awaiting approval.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for report request', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create report request', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while submitting the request.');
        }
    }     

    public function exportToPDF($id)
    {
        $user = Auth::user();
        // جلب كل مزارع المستخدم
        $farms = $user->farms()->pluck('id');
        // البحث عن التقرير في كل مزارع المستخدم
        $report = Report::whereIn('farm_id', $farms)->with('reportDetails')->findOrFail($id);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('report'));
        return $pdf->download('report-' . $report->id . '.pdf');
    }
}