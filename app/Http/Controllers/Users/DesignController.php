<?php

namespace App\Http\Controllers\Users;

use App\Http\Resources\Users\DesignResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\SearchRequest;
use App\Http\Services\Users\DesignService;
use App\Http\Requests\Users\StoreDesignRequest;
use App\Http\Requests\Users\UpdateDesignRequest;
use App\Models\Design;
use Illuminate\Support\Facades\Gate;

class DesignController extends Controller
{
    protected $designService;
    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }
    public function allDesigns(SearchRequest $request)
    {
        $designs = $this->designService->allDesigns($request->validated());
        return $this->success(DesignResource::collection($designs), "All designs retrieved successfully");
    }
    public function myDesigns(SearchRequest $request)
    {
        $designs = $this->designService->myDesigns($request->validated());
        return $this->success(DesignResource::collection($designs), "Designs retrieved successfully");
    }

    public function show($id)
    {
        // Logic to handle showing a specific design
        return $this->success([], "Design details retrieved successfully");
    }

    public function store(StoreDesignRequest $request)
    {
        $designData = $request->validated();
        $design = $this->designService->store($designData);
        return $this->success([new DesignResource($design)], "Design created successfully");
    }

    public function update(UpdateDesignRequest $request, Design $design)
    {
        Gate::authorize('update', $design);
        $design = $this->designService->update($design, $request->validated());
        return $this->success(new DesignResource($design), "Design updated successfully");
    }

    public function destroy(Design $design)
    {
        Gate::authorize('delete', $design);
        $this->designService->destroy($design);
        return $this->success([], "Design deleted successfully");
    }
}
