<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;



class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $siteUrl = parse_url(URL::current(), PHP_URL_SCHEME);

        if (isset($siteUrl)) {
            $urlHost = $siteUrl;
        } else {
            $urlHost = "https";
        }

        $post = $request->all();
        $Method = $request->getMethod();

        $FinalUrl = $request->getUri();
        $log['uri'] = $FinalUrl;
        $log['method'] = $Method;
        $FinalData = [];
        foreach ($post as $Pkey => $pval) :
            $FinalData[] = $Pkey . "=" . $pval;
        endforeach;

        $Scheme = $request->getScheme();
        $Host = $request->getHost();
        $GetBaseUrl = $request->server('REQUEST_URI');
        $ExpUrl = explode("?", $GetBaseUrl);
        $GetBaseUrl = $ExpUrl[0];
        $QueryString = implode("&", $FinalData);
        //$FinalUrl = $Scheme . '://' . $Host . $GetBaseUrl . "?" . $QueryString;
        $FinalUrl = date("Y-m-d H:i:s") . "  " . $Method . "  " . $urlHost . '://' . $Host . $GetBaseUrl . "?" . $QueryString . "\n";

        $BaseUrlFrame = storage_path();
        $fp = fopen($BaseUrlFrame . '/logs/log_' . date("YmdH") . '.txt', 'a');
        fwrite($fp, $FinalUrl);
        fclose($fp);

        return $next($request);
    }
}