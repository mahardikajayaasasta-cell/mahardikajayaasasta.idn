<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KaryawanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isKaryawan()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Karyawan access required.'], 403);
            }
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Karyawan.');
        }

        return $next($request);
    }
}
