<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class TownController extends Controller
{
    public function index()
    {
        $buildings = Building::where('user_id', Auth::id())
                             ->orderBy('grid_y')
                             ->orderBy('grid_x')
                             ->get();

        return view('town.index', compact('buildings'));
    }
}