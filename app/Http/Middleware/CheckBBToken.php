<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckBBToken
{

    private $basic = null;
    //private $apiKey = null;


    public function __construct()
    {
        $this->basic = env('BB_BASIC');
        //$this->apiKey = env('BB_API_KEY');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Cache::has('apiToken')){
            $this->fetchAndStoreToken();
        }

        return $next($request);
    }


    private function fetchAndStoreToken(){
        $response = Http::withHeaders(
            [
                'Authorization' => $this->basic,

            ]
        )->acceptJson()
        ->asForm()
        ->post('https://oauth.hm.bb.com.br/oauth/token',
            [
                'grant_type' => "client_credentials",
                'scope' => "cobrancas.boletos-info cobrancas.boletos-requisicao",
            ]
        );

        $tokenData = json_decode($response->getBody()->getContents());

        Cache::put('apiToken',$tokenData->access_token, 9);
    }
}
