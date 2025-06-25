<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log as Logger;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $user_id = $request->input('user_id');
        $farm_id = $request->input('farm_id');
        $status = $request->input('status');

        $logsQuery = Log::with('user', 'farm');

        // Search
        if ($search) {
            $logsQuery->where('action', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('farm', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
        }

        // Filter by user
        if ($user_id) {
            $logsQuery->where('user_id', $user_id);
        }

        // Filter by farm
        if ($farm_id) {
            $logsQuery->where('farm_id', $farm_id);
        }

        // Filter by status
        if ($status) {
            $logsQuery->where('status', $status);
        }

        // Sort
        if ($sort === 'newest') {
            $logsQuery->latest('timestamp');
        } elseif ($sort === 'oldest') {
            $logsQuery->oldest('timestamp');
        }

        $logs = $logsQuery->paginate(10);

        // Debugging
        if ($logs->isEmpty()) {
            Logger::info('No logs found in AdminLogController index', [
                'query' => $logsQuery->toSql(),
                'bindings' => $logsQuery->getBindings(),
                'request' => $request->all(),
            ]);
        }

        return view('admin.logs.index', compact('logs'));
    }
}