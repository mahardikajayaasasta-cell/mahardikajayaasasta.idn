<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class KaryawanProfileController
{
    public function edit()
    {
        $user = auth()->user();
        return view('karyawan.profile', compact('user'));
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Ensure we have the actual User model instance
            if (!$user instanceof User) {
                $user = User::find($user->id);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                $uploadedFileUrl = Cloudinary::upload(
                    $request->file('photo')->getRealPath(),
                    ['folder' => 'profile_photos']
                )->getSecurePath();
                $user->profile_photo_url = $uploadedFileUrl;
            }

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return redirect()->route('karyawan.profile')->with('success', 'Profile berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Karyawan Profile Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
