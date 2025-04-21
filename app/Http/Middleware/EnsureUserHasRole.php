<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Log attempt to use the middleware
        Log::info('EnsureUserHasRole middleware executed', [
            'role_required' => $role,
            'user_authenticated' => Auth::check(),
            'user_role' => Auth::check() ? Auth::user()->role : 'none'
        ]);
        
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        if ($user->role === $role) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'You do not have permission to access this page.');
    }
} 