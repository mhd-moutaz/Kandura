<?php

namespace App\Http\Controllers\Users;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\Users\AddressService;
use App\Http\Resources\Users\AddressResource;
use App\Http\Requests\Users\StoreAddressRequest;
use App\Http\Requests\Users\UpdateAddressRequest;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{

    protected $addressService;
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'city','district','street','house_number', 'sort_by', 'sort_dir']);
        $addresses = $this->addressService->index($filters);
        return $this->success( AddressResource::collection($addresses),  'Addresses retrieved successfully', 200);
    }
    public function store(StoreAddressRequest $request)
    {
        $address = $this->addressService->store($request->validated());
        return $this->success( new AddressResource($address),  'Address created successfully', 201);
    }
    public function show(string $id)
    {
        //
    }
    public function update(UpdateAddressRequest $request,Address $address)
    {
        Gate::authorize('update',$address);
        $address = $this->addressService->update($request->validated(),$address);
        return $this->success(new AddressResource($address), 'Address updated successfully');
    }
    public function destroy(Address $address)
    {
        Gate::authorize('delete',$address);
        $this->addressService->destroy($address);
        return $this->success(null, 'Address deleted successfully');
    }
}
