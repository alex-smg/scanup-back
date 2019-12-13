<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function index()
    {
        return DB::table('organizations');
    }
    
    public function store(Request $request)
    {

    }
}
