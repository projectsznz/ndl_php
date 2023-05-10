<?php
namespace App\Http\Middleware;
use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class BasicAuth
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
        
        if((empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW'])))
        {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            $response = [
                'success'       => false,
                'message'       => "Authorization Failed - Username and Password Invalid",
                'status'        => 'failure',
                'status_code'    => 401,
                'serverTimezone'=> config('app.timezone'),
                'serverDateTime'=> date('Y-m-d H:i:sa')
            ];
            return response()->json($response, 401);
            exit;
        }

         $user =  User::where([
                                ["email","=",$_SERVER['PHP_AUTH_USER']]
                                
                                ])->get();
                             
         if(count($user)>0)
         {
           

            if(!( Hash::check($_SERVER['PHP_AUTH_PW'],$user[0]['password'])))
            {
               header('HTTP/1.1 401 Authorization Required');
               header('WWW-Authenticate: Basic realm="Access denied"');
               $response = [
                   'success'       => false,
                   'message'       => "Authorization Failed - Username and Password Invalid",
                   'status'        => 'failure',
                   'status_code'    => 401,
                   'serverTimezone'=> config('app.timezone'),
                   'serverDateTime'=> date('Y-m-d H:i:sa')
               ];
               return response()->json($response, 401);
               exit;
            } 
         }
         else
         {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            $response = [
                'success'       => false,
                'message'       => "Authorization Failed - Invalid Username",
                'status'        => 'failure',
                'status_code'    => 401,
                'serverTimezone'=> config('app.timezone'),
                'serverDateTime'=> date('Y-m-d H:i:sa')
            ];
            return response()->json($response, 401);
            exit;
         }
          
         

        // $AUTH_USER = $user[0]['email'];
        // $AUTH_PASS = $user[0]['password'];
         header('Cache-Control: no-cache, must-revalidate, max-age=0');
        
        // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        // $is_not_authenticated = (
        //     !$has_supplied_credentials ||
        //     $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
        //     $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        // );
        // if ($is_not_authenticated) {
        //     header('HTTP/1.1 401 Authorization Required');
        //     header('WWW-Authenticate: Basic realm="Access denied"');
        //     $response = [
        //         'success'       => false,
        //         'message'       => "Authorization Failed",
        //         'status'        => 'failure',
        //         'status_code'    => 401,
        //         'serverTimezone'=> config('app.timezone'),
        //         'serverDateTime'=> date('Y-m-d H:i:sa')
        //     ];
        //     return response()->json($response, 401);
        //     exit;
        // }
        return $next($request);
    }
}