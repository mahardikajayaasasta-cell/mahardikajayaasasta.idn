<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Perlu izin Admin.'], 403);
            }
            
            // Redirect ke dashboard karyawan jika bukan admin, jangan tampilkan halaman Forbidden yang kaku
            return redirect()->route('karyawan.dashboard')
                ->with('error', 'Akses ditolak. Halaman tersebut hanya bisa diakses oleh Admin.');
        }

        return $next($request);
    }
}
