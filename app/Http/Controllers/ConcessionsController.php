<?php

namespace App\Http\Controllers;

use App\Models\Concessions;
use Illuminate\Http\Request;

class ConcessionsController extends Controller
{
    public function add()
    {
        return view('concessions.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('concessions', 'public');
        }

        Concessions::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'image' => $imagePath,
        ]);

        return redirect()->route('concession.view')->with('success', 'Concession added successfully!');
    }

    public function show()
    {
        $concessions = Concessions::all();

        return view('concessions.view', compact('concessions'));
    }

    public function edit($id)
    {
        $concessions = Concessions::findOrFail($id);

        return view('concessions.edit', compact('concessions'));
    }
}