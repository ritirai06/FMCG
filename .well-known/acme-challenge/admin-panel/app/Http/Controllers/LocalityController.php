<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locality;
class LocalityController extends Controller
{
   public function getLocalities($city_id)
{
    $localities = Locality::where('city_id', $city_id)->get();

    return response()->json($localities);
}
}
