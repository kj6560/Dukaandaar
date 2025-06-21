<?php

namespace App\Http\Middleware;

use App\Models\UserFeaturePurchase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $activeSubscription = UserFeaturePurchase::where('org_id', $user->org_id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
        
        if (!empty($activeSubscription->id) && $activeSubscription->expired != 1) {
            return $next($request);
        }else{
            return response()->json([
            'statusCode' => 200,
            'message' => "You don't have an active subscription plz contact Admin",
            'data' => [],
        ], 200);
        }
    }
}
