<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GeoIp2\Database\Reader;
use GuzzleHttp\Client;

class BasicController extends Controller
{

    public function greet(Request $request)
    {
        // Get visitor name from query parameter
        $visitorName = $request->input('visitor_name', 'Guest');

        // Get client IP address
        $clientIp = $request->ip();

        // Fetch location based on IP address (using a service like ip-api.com)
        $location = $this->getLocationByIp($clientIp);

        // Fetch current temperature for the location (using a weather API like OpenWeatherMap)
        $temperature = $this->getCurrentTemperature($location['city']);

        // Prepare response data
        $response = [
            'client_ip' => $clientIp,
            'location' => $location['city'],
            'greeting' => "Hello, $visitorName! The temperature is $temperature degrees Celsius in {$location['city']}."
        ];

        return response()->json($response);
    }

    private function getLocationByIp($ip)
    {
        $client = new Client();
        $response = $client->get("http://ip-api.com/json/$ip");
        $data = json_decode($response->getBody(), true);

        return [
            'city' => $data['city']
        ];
    }

    private function getCurrentTemperature($city)
    {
        $apiKey = env('OPENWEATHERMAP_API_KEY');
        $client = new Client();
        $response = $client->get("http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric");
        $data = json_decode($response->getBody(), true);

        return $data['main']['temp'];
    }
}
    
    // public function hello(Request $request)
    // {
    //     $visitor_name = $request->query('visitor_name');

    //     $client_ip = $request->ip();
    //     $location = Geo::getLocation($client_ip);

    //     $temperature = OpenWeather::getTemperature($location['lat'], $location['lon']);

    //     return response()->json([
    //         'client_ip' => $client_ip,
    //         'location' => "{$location['city']}, {$location['country']}",
    //         'greeting' => "Hello, {$visitor_name}!, the temperature is {$temperature} degrees Celcius in {$location['city']}"
    //     ]);
    // }
    // public function hello(Request $request)
    // {
    //     $visitorName = $request->input('visitor_name');
    //     $clientIp = $request->ip();

    //     $reader = new Reader(database_path('geoip.mmdb'));
    //     $record = $reader->city($clientIp);

    //     $city = $record->city->name;
    //     $temperature = rand(10, 20); // dummy temperature value

    //     $greeting = "Hello, $visitorName!, the temperature is $temperature degrees Celcius in $city";

    //     return response()->json([
    //         'client_ip' => $clientIp,
    //         'location' => $city,
    //         'greeting' => $greeting,
    //     ]);
    // }

//     public function index(Request $request)
//     {
//         $visitorName = $request->input('visitor_name');
//         $clientIp = $request->ip();
//         $location = $this->getLocation($clientIp);
//         $temperature = $this->getTemperature($location);
//         $greeting = "Hello, $visitorName!, the temperature is $temperature degrees celcius in $location";

//         return response()->json([
//             'client_ip' => $clientIp,
//             'location' => $location,
//             'greeting' => $greeting
//         ]);
//     }

//     private function getLocation($ip)
//     {
//         $response = Http::get("http://ip-api.com/json/$ip");
//         $data = $response->json();
//         return $data['city'];
//     }

//     private function getTemperature($location)
//     {
//         $apiKey = 'YOUR_OPENWEATHERMAP_API_KEY';
//         $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
//             'q' => $location,
//             'appid' => $apiKey,
//             'units' => 'metric',
//         ]);
//         $data = $response->json();
//         return $data['main']['temp'];
//     }
// }
