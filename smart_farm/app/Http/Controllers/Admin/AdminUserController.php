<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; // إضافة هذا السطر

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $usersQuery = User::with('farms');

        // البحث
        if ($search) {
            $usersQuery->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
        }

        // الترتيب
        if ($sort === 'newest') {
            $usersQuery->latest();
        } elseif ($sort === 'oldest') {
            $usersQuery->oldest();
        }

        $users = $usersQuery->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,farmer_manager',
                'is_active' => 'required|boolean',
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->is_active = $request->is_active;
            $user->save();

            Log::info('User created successfully', ['user_id' => $user->id]);
            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for user creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create user', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|in:admin,farmer_manager',
                'is_active' => 'required|boolean',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->is_active = $request->is_active;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $updated = DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'password' => $user->password,
                    'updated_at' => now(),
                ]);

            if ($updated === 0) {
                throw new \Exception('No changes were saved.');
            }

            Log::info('User updated successfully', ['user_id' => $user->id]);
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for user update', ['user_id' => $user->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            Log::info('User deleted successfully', ['user_id' => $id]);
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user', ['user_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.users.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}