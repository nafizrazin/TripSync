<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
final class RequestId {
    public function handle(Request $request, Closure $next): Response {
        $requestId=(string)($request->headers->get('X-Request-ID') ?: 'REQ-'.Str::upper(Str::random(12)));
        $request->attributes->set('request_id',$requestId);
        $response=$next($request);
        $response->headers->set('X-Request-ID',$requestId);
        return $response;
    }
}
