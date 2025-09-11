<?php

namespace App\Providers;

use App\Support\Security\Health as SecurityHealth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Log security features activation with colorized console output
        $this->logSecurityFeaturesEnabled();
    }
    
    /**
     * Log that security features have been enabled and their health status
     * - Green (\e[32m) when OK
     * - Red   (\e[31m) when Failure
     *
     * @return void
     */
    private function logSecurityFeaturesEnabled(): void
    {
        $checks = SecurityHealth::checks();

        // Aggregate overall status
        $allOk = collect($checks)->every(fn ($r) => $r['ok'] === true);

        // Structured log to laravel.log (no ANSI colors)
        Log::info('🔒 Security features enabled', [
            'overall_ok' => $allOk,
            'checks' => $checks,
        ]);

        // Colorized console output
        $GREEN = "\e[32m"; // green
        $RED   = "\e[31m"; // red
        $RESET = "\e[0m";  // reset

        $prefix = $allOk ? "{$GREEN}[SECURITY]{$RESET}" : "{$RED}[SECURITY]{$RESET}";
        error_log($prefix . ' Boot checks: ' . ($allOk ? ($GREEN . 'OK' . $RESET) : ($RED . 'FAIL' . $RESET)));

        foreach ($checks as $name => $result) {
            $color = $result['ok'] ? $GREEN : $RED;
            $status = $result['ok'] ? 'OK' : 'FAIL';
            error_log(sprintf('%s %s: %s - %s%s', $prefix, $name, $status, $color, $result['message'] . $RESET));
        }

        if (!$allOk) {
            error_log($RED . '[SECURITY] One or more security checks failed. Review configuration before proceeding to production.' . $RESET);
        }
    }
}
