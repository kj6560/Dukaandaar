<?php

namespace App\Http\Middleware;

use App\Models\UserFeaturePurchase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $role = $user->role;

        $activeSubscription = UserFeaturePurchase::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where('expired', 0)
            ->first();

        if (!empty($activeSubscription) || $role == 1) {
            return $next($request);
        }
        return redirect()->route('subscription.purchase')->with('error', 'Your subscription has expired or is inactive.');
    }
}
