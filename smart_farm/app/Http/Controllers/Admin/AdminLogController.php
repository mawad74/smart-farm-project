<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // إضافة هذا السطر

class AdminLogController extends Controller
{
    public function index()
    {
        $logs = Log::with('user', 'farm')->latest()->paginate(10); // جلب السجلات مع بيانات المستخدم والمزرعة
        return view('admin.logs.index', compact('logs'));
    }
}