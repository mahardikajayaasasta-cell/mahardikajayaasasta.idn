<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanProfileController
{
    public function edit()
    {
        $user = auth()->user();
        return view('karyawan.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $uploadedFileUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload(
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
    }
}
