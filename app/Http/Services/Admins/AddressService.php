<?php

namespace App\Http\Services\Admins;

use App\Models\Address;
use App\Models\City;

class AddressService
{
    public function index($filters)
    {
        return Address::with('city')->filter($filters)->paginate(5)->withQueryString();
    }
    public function getAllCities()
    {
        return City::all();
    }
}
