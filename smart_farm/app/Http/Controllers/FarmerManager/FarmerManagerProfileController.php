<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FarmerManagerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager'); // Middleware للـ Farmer Manager
    }

    public function edit()
    {
        $user = Auth::user();
        return view('farmer_manager.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            // التحقق من البيانات
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            // تسجيل البيانات قبل التحديث
            Log::info('Before updating user profile', [
                'user_id' => $user->id,
                'current_name' => $user->name,
                'current_email' => $user->email,
                'new_name' => $request->name,
                'new_email' => $request->email,
                'has_password' => $request->filled('password') ? 'Yes' : 'No',
            ]);

            // تحديث البيانات باستخدام Query Builder
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
                Log::info('Password updated for user', ['user_id' => $user->id]);
            }

            $updated = DB::table('users')
                ->where('id', $user->id)
                ->update($data);

            if ($updated === 0) {
                Log::error('No rows updated for user', ['user_id' => $user->id]);
                throw new \Exception('No changes were saved. The data might be the same.');
            }

            Log::info('User profile updated successfully using Query Builder', ['user_id' => $user->id]);
            return redirect()->route('farmer_manager.profile.edit')->with('success', 'Profile updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for user profile update', [
                'user_id' => $user->id,
                'errors' => $e->errors(),
            ]);
            return redirect()->route('farmer_manager.profile.edit')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update user profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('farmer_manager.profile.edit')->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
}