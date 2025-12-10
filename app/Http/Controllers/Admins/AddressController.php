<?php

namespace App\Http\Controllers\Admins;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Admins\AddressService;

class AddressController extends Controller
{
    protected $addressService;
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'city','sort_dir']);
        $addresses = $this->addressService->index($filters);
        $cities = $this->addressService->getAllCities();
        return view('admin.addresses.index', compact('addresses', 'cities'));
    }
}
