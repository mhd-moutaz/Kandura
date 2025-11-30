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
    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.addresses.edit', compact(var_name: 'user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {

    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {

    }
}
