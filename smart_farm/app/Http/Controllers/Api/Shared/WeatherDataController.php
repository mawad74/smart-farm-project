<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class WeatherDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WeatherData  $weatherData
     * @return \Illuminate\Http\Response
     */
    public function show(WeatherData $weatherData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WeatherData  $weatherData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WeatherData $weatherData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WeatherData  $weatherData
     * @return \Illuminate\Http\Response
     */
    public function destroy(WeatherData $weatherData)
    {
        //
    }
}
