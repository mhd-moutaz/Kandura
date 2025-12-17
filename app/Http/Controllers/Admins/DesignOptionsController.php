<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\StoreDesignOptionsRequest;
use App\Http\Requests\Admins\UpdateDesignOptionsRequest;
use App\Http\services\Admins\DesignOptionService;
use App\Models\DesignOption;
use Illuminate\Support\Facades\Gate;

class DesignOptionsController extends Controller
{
    protected $designOptionService;
    public function __construct(DesignOptionService $designOptionService)
    {
        $this->designOptionService = $designOptionService;
    }
    public function index()
    {
        $designOptions = $this->designOptionService->index();
        // dd($designOptions);
        return view('admin.design_options.index', compact('designOptions'));
    }
    public function create()
    {
        return view('admin.design_options.create');
    }
    public function store(StoreDesignOptionsRequest $request){
        $this->designOptionService->store($request->validated());
        return redirect()->route('designOptions.index')->with('success', 'Design option created successfully.');
    }
    public function edit(DesignOption $designOption)
    {
        return view('admin.design_options.edit', compact('designOption'));
    }
    public function update(UpdateDesignOptionsRequest $request, DesignOption $designOption)
    {
        $this->designOptionService->update($designOption, $request->validated());
        return redirect()->route('designOptions.index')->with('success', 'Design option updated successfully.');
    }
    public function destroy(DesignOption $designOption)
    {
        $designOption->delete();
        return redirect()->route('designOptions.index')->with('success', 'Design option deleted successfully.');
    }
}
