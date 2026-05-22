<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // 1. Jika punya akses, langsung lanjut.
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 2. Jika TIDAK punya akses, jangan lempar ke dashboard admin lagi.
        // Arahkan ke halaman utama atau halaman profil yang universal.
        if ($userRole === 'superadmin' || $userRole === 'admin') {
            // Jika admin nyasar ke rute customer, balikin ke dashboard admin
            // TAPI pastikan rute ini tidak looping
            if ($request->is('admin/*')) {
                abort(403, 'Anda tidak memiliki otoritas untuk halaman ini.');
            }
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
    }
}
