<?php


namespace App\Http\Middleware;


use App\User;
use Illuminate\Support\Facades\Auth;

class ContentSecurityPolicy
{


    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        if(isset($request->shop))
        $user=User::where('name',$request->shop)->orWhere('password',$request->token)->first();
        else
        $user=Auth::user();

        if($user)
        {

            $url="frame-ancestors https://".$user->name." https://admin.shopify.com";
            $response->headers->set('Content-Security-Policy',$url);


        }else {
            if($response->exception==null)
            {

                if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!="") {
                    parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
                }
                if(isset($queries['shop']))
                {
                    $url="frame-ancestors https://".$queries['shop']." https://admin.shopify.com";

                    $response->headers->set('Content-Security-Policy',$url);
                }




            }


        }
        return $response;

    }
}
