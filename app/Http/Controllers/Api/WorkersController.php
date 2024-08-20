<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class WorkersController extends Controller
{
    public function index() {
        $workers = User::all();
        return response()->json($workers);
    }
}
