<?php

namespace App\Http\Controllers\Users;

use App\Models\Design;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Services\Users\DesignService;
use App\Http\Resources\Users\DesignResource;
use App\Http\Requests\Users\StoreDesignRequest;
use App\Http\Requests\Users\UpdateDesignRequest;
use App\Http\Requests\Global\SearchDesignsRequest;

class DesignController extends Controller
{
    protected $designService;
    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }
    public function allDesigns(SearchDesignsRequest $request)
    {
        $designs = $this->designService->allDesigns($request->validated());
        return $this->success(DesignResource::collection($designs), "All designs retrieved successfully");
    }
    public function show(SearchDesignsRequest $request)
    {
        $designs = $this->designService->myDesigns($request->validated());
        return $this->success(DesignResource::collection($designs), "Designs retrieved successfully");
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
