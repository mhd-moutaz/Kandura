<?php

namespace App\Http\Services\Users;

use App\Models\City;
use App\Exceptions\GeneralException;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressService
{
    public function index(array $filters, int $perPage = 15)
    {
        return Address::with('city')
            ->filter($filters)
            ->paginate($perPage)
            ->withQueryString();
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(array $data)
    {
        $data['user_id'] = Auth::id();
        $city = City::byName($data['city'])->first();
        if (! $city) {
            throw new GeneralException('City not found');
        }
        $data['city_id'] = $city->id;
        unset($data['city']);
        $address = Address::create($data);
        return $address;
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(array $data, $address)
    {
        if(isset($data['city'])){
            $city = City::byName($data['city'])->first();
            if (! $city) {
                throw new GeneralException('City not found');
            }
            $data['city_id'] = $city->id;
            unset($data['city']);
        }
        $address->update($data);
        return $address;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($address)
    {
        $address->delete();
    }

}
