<?php 

namespace App\Http\Utils\RateLimiter;

use Illuminate\Support\Facades\RateLimiter;

class Limitator
{
    public static function attempt(string $key, int $maxAttempts, callable $callback)
    {
        $executed = RateLimiter::attempt(
            $key,
            $maxAttempts,
            function () use ($callback) {
                return $callback();
            }
        );

        if (! $executed) {
            return response()->json([
                'message' => 'Too many attempts. Please try again in ' . RateLimiter::availableIn($key) . ' seconds.'
            ], 429);
        }

        return $executed;
    }

    public static function tooManyAttempts(
        string $key,
        int $maxAttempts
    ): bool {
        return RateLimiter::tooManyAttempts(
            $key,
            $maxAttempts
        );
    }

    public static function hit(
        string $key,
        int $decaySeconds = 60
    ): void {
        RateLimiter::hit(
            $key,
            $decaySeconds
        );
    }

    public static function clear(string $key): void
    {
        RateLimiter::clear($key);
    }

    public static function availableIn(string $key): int
    {
        return RateLimiter::availableIn($key);
    }
}