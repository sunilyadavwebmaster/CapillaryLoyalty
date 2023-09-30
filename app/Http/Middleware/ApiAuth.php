<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->isCors($request)) {
            return $next($request);
        } else {
            if ($this->isAuthenticated($request->getUser(),$request->getPassword())) {
                return $next($request);
            } else {
                return response('Invalid Access', 401, ['WWW-Authenticate' => 'Basic']);
            }
        }
    }

    private function isCors($request)
    {
        $domain = $request->header('origin');
        $domain = $this->removeHttp($domain);
        \Log::info('Request from : ' . $domain);
        if ($domain) {
            return User::where(function ($query) use ($domain) {
                $query->where('name', $domain)->orWhere('alternate_name', $domain);
            })->exists();
        }
        return false;
    }
   private function removeHttp($url) {
        return parse_url($url,PHP_URL_HOST);
}

    private function isAuthenticated($username,$password)
    {

        if (!$username || !$password) {
            return false;
        }

        $apiUser = env('API_USER');
        $apiPassword = env('API_PASSWORD');

        return $username === $apiUser && $password === $apiPassword;
    }
}