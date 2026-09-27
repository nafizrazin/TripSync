<?php
namespace App\Providers;
use App\Contracts\PaymentGateway; use App\Infrastructure\Payments\SimulationGateway; use Illuminate\Cache\RateLimiting\Limit; use Illuminate\Http\Request; use Illuminate\Support\Facades\RateLimiter; use Illuminate\Support\ServiceProvider;
final class AppServiceProvider extends ServiceProvider {
    public function register(): void { $this->app->bind(PaymentGateway::class,fn($app)=>match(config('services.payments.provider',env('PAYMENT_PROVIDER','simulation'))){'simulation'=>new SimulationGateway($app->make(\App\Domain\Booking\FareCalculator::class)),default=>throw new \RuntimeException('Unsupported PAYMENT_PROVIDER')}); }
    public function boot(): void { RateLimiter::for('login',fn(Request $request)=>Limit::perMinute(5)->by(strtolower((string)$request->input('email')).'|'.$request->ip())); RateLimiter::for('register',fn(Request $request)=>Limit::perMinute(4)->by($request->ip())); }
}
