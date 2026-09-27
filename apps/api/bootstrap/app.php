<?php
use App\Exceptions\ApiProblem;
use App\Http\Middleware\{RequestId,RequireRole};
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions,Middleware};
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void { $middleware->trustProxies(at:'*'); $middleware->append(RequestId::class); $middleware->alias(['role'=>RequireRole::class]); })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ApiProblem $e, Request $request) { return response()->json(['message'=>$e->getMessage(),'code'=>$e->errorCode,'context'=>$e->context ?: null,'request_id'=>$request->attributes->get('request_id')],$e->status); });
        $exceptions->render(function (ValidationException $e, Request $request) { if($request->is('api/*')) return response()->json(['message'=>'The submitted data is invalid.','code'=>'VALIDATION_ERROR','errors'=>$e->errors(),'request_id'=>$request->attributes->get('request_id')],422); });
        $exceptions->render(function (Throwable $e, Request $request) { if($request->is('api/*') && !$e instanceof HttpExceptionInterface) return response()->json(['message'=>'An unexpected error occurred.','code'=>'INTERNAL_ERROR','request_id'=>$request->attributes->get('request_id')],500); });
    })->create();
