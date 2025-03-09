<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureTemplateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add security headers
        // $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://maps.google.com/; style-src 'self' 'unsafe-inline' https://unpkg.com https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data: https://images.unsplash.com; frame-src 'self' https://maps.google.com/ https://www.google.com/;");
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
