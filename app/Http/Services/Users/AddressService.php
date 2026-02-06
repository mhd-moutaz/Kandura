<?php

namespace App\Http\Services\Users;

use App\Models\City;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
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
     * Store a newly created address.
     */
    public function store(array $data): Address
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = Auth::id();
            $data['city_id'] = $this->getCityIdByName($data['city']);
            unset($data['city']);

            return Address::create($data)->load('city');
        });
    }

    /**
     * Update the specified address.
     */
    public function update(array $data, Address $address): Address
    {
        return DB::transaction(function () use ($data, $address) {
            if (isset($data['city'])) {
                $data['city_id'] = $this->getCityIdByName($data['city']);
                unset($data['city']);
            }

            $address->update($data);
            return $address->fresh('city');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($address)
    {
        $address->delete();
    }

    private function getCityIdByName(string $cityName): int
    {
        $city = City::byName($cityName)->first();
        if (!$city) {
            throw new GeneralException('City not found');
        }
        return $city->id;
    }
}
