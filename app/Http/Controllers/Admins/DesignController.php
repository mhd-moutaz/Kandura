<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Global\SearchRequest;
use App\Http\Services\Admins\DesignService;
use App\Models\Design;


class DesignController extends Controller
{
    protected $designService;
    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }
    public function index(SearchRequest $request)
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
}
