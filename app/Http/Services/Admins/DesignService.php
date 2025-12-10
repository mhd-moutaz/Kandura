<?php
namespace App\Http\Services\Admins;

use App\Models\Design;
use App\Models\DesignOption;
use Illuminate\Http\Request;


class DesignService{
    public function index($data)
    {

        $designOptions = DesignOption::all();
        $design = Design::filter($data)->paginate(9)->withQueryString(); // Return an array of designs for admin view
        // dd($data['search']);
        return ['designs' => $design, 'designOptions' => $designOptions];
    }
}
