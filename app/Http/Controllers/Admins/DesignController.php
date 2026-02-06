<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Services\Admins\DesignService;
use App\Models\Design;
use App\Http\Requests\Global\SearchDesignsRequest;
use App\Http\Requests\Admins\UpdateDesignQuantityRequest;
use App\Exceptions\GeneralException;


class DesignController extends Controller
{
    protected $designService;
    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }
    public function index(SearchDesignsRequest $request)
    {
        $data = $this->designService->index($request->validated());
        $designs = $data['designs'];
        $designOptions = $data['designOptions'];
        return view('admin.designs.index', compact('designs', 'designOptions'));
    }

    /**
     * Display the specified design details for admin.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $design = Design::with(['designImages', 'measurements', 'user'])->findOrFail($id);
        return view('admin.designs.show', compact('design'));
    }

    /**
     * Toggle design state (active/inactive)
     *
     * @param  Design  $design
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleState(Design $design)
    {
        try {
            $result = $this->designService->toggleState($design);
            return back()->with('success', $result['message']);
        } catch (GeneralException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update design quantity
     *
     * @param  UpdateDesignQuantityRequest  $request
     * @param  Design  $design
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateQuantity(UpdateDesignQuantityRequest $request, Design $design)
    {
        try {
            $result = $this->designService->updateQuantity($design, $request->validated());
            return back()->with('success', $result['message']);
        } catch (GeneralException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
