<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimit
{
    private const MAX_ATTEMPTS = 2;
    private const DECAY_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = "login_attempts:{$ip}";
        $blockedKey = "{$key}:blocked_until";

        // Verifica se está bloqueado (o cache expira automaticamente)
        if (Cache::has($blockedKey)) {
            $remainingMinutes = Cache::get($blockedKey);

            return response()->json(
                ['message' => "Muitas tentativas de login. Tente novamente em {$remainingMinutes} minuto(s)."],
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        /** @var Response $response */
        $response = $next($request);

        // Se falhou (status diferente de 2xx), incrementa tentativas
        if (
            $response->getStatusCode() < Response::HTTP_OK || 
            $response->getStatusCode() >= Response::HTTP_MULTIPLE_CHOICES
        ) {
            $this->recordFailedAttempt($key, $blockedKey);
        } else {
            // Sucesso: limpa as tentativas
            Cache::forget($key);
            Cache::forget($blockedKey);
        }

        return $response;
    }

    private function recordFailedAttempt(string $key, string $blockedKey): void
    {
        $attempts = Cache::increment($key, 1);

        // Se esta é a primeira tentativa, define o TTL
        if ($attempts === 1) {
            Cache::put($key, 1, now()->addMinutes(self::DECAY_MINUTES));
        }

        // Se atingiu o máximo de tentativas, bloqueia
        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put($blockedKey, self::DECAY_MINUTES, now()->addMinutes(self::DECAY_MINUTES));
        }
    }
}