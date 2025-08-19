<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index()
    {
        return view('example.index');
    }

    public function store(Request $request)
    {
        // Handle the POST request data here
        // Example: $data = $request->all();

        return response()->json(['message' => 'Data received', 'data' => $request->all()]);
    }
}
