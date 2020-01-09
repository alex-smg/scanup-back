<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Person;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string   $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        $token = $request->header('token');

        if(!$token) {
            return response()->json(['error' => 'Token not provided.'], 401);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json(['error' => 'Provided token is expired.'], 400);
        } catch(Exception $e) {
            return response()->json(['error' => 'An error while decoding token.'], 400);
        }

        $person = Person::find($credentials->sub);

        if (!$person->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
