<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\ControlCommand;
use App\Models\Actuator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerControlCommandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager'); // Middleware للـ Farmer Manager
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $actuator_id = $request->input('actuator_id');
        $status = $request->input('status');

        $commandsQuery = ControlCommand::where('user_id', $user->id)->with('actuator', 'user');

        // البحث
        if ($search) {
            $commandsQuery->where('command_type', 'like', "%{$search}%")
                          ->orWhereHas('user', function ($query) use ($search) {
                              $query->where('name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('actuator', function ($query) use ($search) {
                              $query->where('type', 'like', "%{$search}%");
                          });
        }

        // فلترة حسب الجهاز
        if ($actuator_id) {
            $commandsQuery->where('actuator_id', $actuator_id);
        }

        // فلترة حسب الحالة
        if ($status !== null) {
            $commandsQuery->where('status', $status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $commandsQuery->latest();
        } elseif ($sort === 'oldest') {
            $commandsQuery->oldest();
        }

        $commands = $commandsQuery->paginate(10);
        $actuators = Actuator::all(); // جلب كل الأجهزة للفلترة
        $users = User::where('role', 'farmer_manager')->get(); // جلب المستخدمين اللي دورهم Farmer Manager للفلترة

        return view('farmer_manager.control-commands.index', compact('commands', 'actuators', 'users'));
    }

    public function create()
    {
        $user = auth()->user();
        $actuators = Actuator::all();
        $users = User::where('role', 'farmer_manager')->get();
        return view('farmer_manager.control-commands.create', compact('actuators', 'users', 'user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        try {
            $validated = $request->validate([
                'actuator_id' => 'required|exists:actuators,id',
                'user_id' => 'required|exists:users,id',
                'command_type' => 'required|string|max:255',
                'executed_at' => 'required|date',
                'status' => 'required|boolean',
            ]);

            $command = new ControlCommand();
            $command->actuator_id = $request->actuator_id;
            $command->user_id = $user->id; // يتم تعيين user_id تلقائيًا بناءً على المستخدم الحالي
            $command->command_type = $request->command_type;
            $command->executed_at = $request->executed_at;
            $command->status = $request->status;
            $command->save();

            Log::info('Control Command created by Farmer Manager', ['command_id' => $command->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.control-commands.index')->with('success', 'Control Command created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for control command creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create control command', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create control command: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $command = ControlCommand::where('user_id', $user->id)->findOrFail($id);
        $actuators = Actuator::all();
        $users = User::where('role', 'farmer_manager')->get();
        return view('farmer_manager.control-commands.edit', compact('command', 'actuators', 'users'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $command = ControlCommand::where('user_id', $user->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'actuator_id' => 'required|exists:actuators,id',
                'user_id' => 'required|exists:users,id',
                'command_type' => 'required|string|max:255',
                'executed_at' => 'required|date',
                'status' => 'required|boolean',
            ]);

            $command->actuator_id = $request->actuator_id;
            $command->user_id = $user->id; // يتم تعيين user_id تلقائيًا
            $command->command_type = $request->command_type;
            $command->executed_at = $request->executed_at;
            $command->status = $request->status;
            $command->save();

            Log::info('Control Command updated by Farmer Manager', ['command_id' => $command->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.control-commands.index')->with('success', 'Control Command updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for control command update', ['command_id' => $command->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update control command', ['command_id' => $command->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update control command: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $command = ControlCommand::where('user_id', $user->id)->findOrFail($id);

        try {
            $command->delete();
            Log::info('Control Command deleted by Farmer Manager', ['command_id' => $id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.control-commands.index')->with('success', 'Control Command deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete control command', ['command_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.control-commands.index')->with('error', 'Failed to delete control command: ' . $e->getMessage());
        }
    }
}