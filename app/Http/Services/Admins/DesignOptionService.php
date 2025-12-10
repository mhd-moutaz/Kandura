<?php

namespace App\Http\services\Admins;

use App\Models\DesignOption;

class DesignOptionService
{
    public function index()
    {
        return DesignOption::all();
    }
    public function store($data)
    {
        DesignOption::create($data);
    }
    public function update(DesignOption $designOption, $data)
    {
        $designOption->update($data);
    }
}
