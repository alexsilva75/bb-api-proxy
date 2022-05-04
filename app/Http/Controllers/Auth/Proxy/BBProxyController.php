<?php

namespace App\Http\Controllers\Auth\Proxy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BBProxyController extends Controller
{
    //
    private $basic = null; // env('BB_BASIC');

    private $baseURL = 'https://api.hm.bb.com.br/cobrancas/v2';
    private $apiKey = null; //env('BB_API_KEY');


    public function __construct()
    {
        $this->basic = env('BB_BASIC');
        $this->apiKey = env('BB_API_KEY');
    }
    public function getToken(Request $request){

        //dd($request->all());
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
        //dd($tokenData->access_token);

        return response()->json(['data' =>$response->getBody()->getContents()]);
    }

    public function boletos(Request $request){

        $token = Cache::get('apiToken');

        $data = $request->json()->all();

        //return response()->json(['pagador' => $data['pagador']]);
        //dd($data['numeroConvenio']);

        $response = Http::withHeaders(
            [
                'Authorization' => "Bearer $token",

            ]
        )->acceptJson()
        //->asForm()
        ->post("{$this->baseURL}/boletos?gw-dev-app-key={$this->apiKey}",
             json_encode($data )
        );

        return json_decode($response->getBody()->getContents(), JSON_UNESCAPED_SLASHES);
    }



}
