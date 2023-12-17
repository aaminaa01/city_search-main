<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\City;
use MongoDB;

class CityController extends Controller
{

    public function getClosestCities($selectedCityId)
    {   
        
        // Get the city details based on the cityId
        $selectedCity = DB::connection('mongodb')->collection('city-search')->get();
            foreach ($selectedCity as $loc) {
                if($loc['locId']==$selectedCityId){
                    $selectedCity = $loc;
                    break;
                }
            }
        if (!$selectedCity) {
            // Handle the case where the city is not found
            return response()->json(['error' => 'City not found'], 404);
        }


        $selectedLat = $selectedCity['latitude'];
        $selectedLon = $selectedCity['longitude'];

        // Get the 5 closest cities based on the great circle distance formula
        $closestCities = $this->getClosestCitiesFromDatabase($selectedLat, $selectedLon);

        // Return the data as JSON
        return $closestCities;
    }

    private function getClosestCitiesFromDatabase($lat, $lon)
    {
        // Implement the great circle distance formula (Haversine formula)
        // and fetch the 5 closest cities from the database based on the coordinates

        $locations = DB::connection('mongodb')->collection('city-search')->get();

        $locations = $locations->map(function ($location) use ($lat, $lon) {
            $location['distance'] = $this->haversineDistance($lat, $lon, $location['latitude'], $location['longitude']);
            // return $location;
            return [
                'locId' => $location['locId'],
                'country' => $location['country'],
                'region' => $location['region'],
                'city' => $location['city'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'distance' => $location['distance']
            ];
        });
        $locations = $locations->toArray();
        usort($locations, function ($a, $b) {
            return $a['distance'] - $b['distance'];
        });

        return array_slice($locations, 1, 5);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; 

        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);

        $a = sin($dlat / 2) * sin($dlat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; 

        return $distance;
    }



    public function index(Request $request){ 
        $minutes = 20000000;       
        // Check if cities are already in cache
        $cities = Cache::remember('cities', $minutes, function () {
            // Fetch cities from the database if not in cache
            return DB::connection('mongodb')->collection('city-search')->get();
        });

        return view('index', compact('cities'));
    }

    
   
}
