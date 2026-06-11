<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class GuideController extends Controller
{
    public function index()
    {
        $buildings = Building::where('user_id', Auth::id())->get();
        return view('guide.index', compact('buildings'));
    }
}