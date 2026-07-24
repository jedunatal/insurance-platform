<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * O caminho para a rota "home" da sua aplicação.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define as amarrações de modelo de rota, filtros de padrão, etc.
     */
    public function boot(): void
    {
        // Rate Limiter para a API (Mantido e protegido)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('web')
                ->prefix('web')
                ->group(base_path('routes/web.php'));

            // 3. Carrega o seu módulo isolado de Leads (COMPLEMENTADO)
            Route::middleware('web')
                ->prefix('leads')
                ->group(base_path('routes/leads.php'));

            Route::middleware('web')
                ->prefix('insureds')
                ->group(base_path('routes/insureds.php'));
        });
    }
}