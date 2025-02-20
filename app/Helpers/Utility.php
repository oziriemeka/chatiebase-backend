<?php
namespace App\Helpers;

use App\Services\GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Request;


const FALLBACK_COUNTRY = "US";
const FALLBACK_TIMEZONE = "America/New_York";
const FALLBACK_LOCATION = [
    "country_code" => FALLBACK_COUNTRY,
    "timezone" => FALLBACK_TIMEZONE
];
function getUserLocation(Request $request)
{
    $ip = $request->ip();

    if ($ip == "127.0.0.1" || $ip == "::1") {
        return response()->json(FALLBACK_LOCATION);
    }

    $client = new GuzzleClient();

    try {
        $response = $client->get("http://www.geoplugin.net/json.gp?ip={$ip}");
        $data = json_decode($response->getBody()->getContents(), true);

        if(isset($data['geoplugin_countryCode'] ) && isset($data['geoplugin_timezone'])) {
            return response()->json([
                'country_code' => $data['geoplugin_countryCode'],
                'timezone' => $data['geoplugin_timezone'],
            ]);
        } else {
            return response()->json(FALLBACK_LOCATION);
        }

    } catch (\Exception $e) {
        //Todo : Log Error $e->getMessage()
    } catch (GuzzleException $e) {
        //Todo : Log Guzzle related error $e->getMessage()
    }
}



