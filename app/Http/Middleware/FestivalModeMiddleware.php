<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class FestivalModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if festival mode is enabled
        $festivalMode = Cache::get('festival_mode', false);
        $priceMultiplier = Cache::get('festival_price_multiplier', 1.5);

        // Share festival mode data with all views
        view()->share('festivalMode', $festivalMode);
        view()->share('festivalPriceMultiplier', $priceMultiplier);

        // Add festival mode indicator to request
        $request->merge([
            'festival_mode' => $festivalMode,
            'festival_price_multiplier' => $priceMultiplier,
        ]);

        return $next($request);
    }
}
