<?php

namespace App\Http\Controllers;

use App\Models\Concessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $concession = Concessions::findOrFail($id);
        
        $concession->name = $request->input('name');
        $concession->description = $request->input('description');
        $concession->price = $request->input('price');

        if ($request->hasFile('image')) {

            if (!empty($concession->image)) {
                Storage::delete("public/{$concession->image}");
            }
            $imagePath = $request->file('image')->store('concessions', 'public');
            $concession->image = $imagePath;
        }

        $concession->save();

        return redirect()->route('concession.view')->with('success', 'Concession updated successfully!');
    }

    public function delete($id)
    {
        $concession = Concessions::findOrFail($id);
        $concession->delete();

        return response()->json(['message' => 'Concession deleted successfully']);
    }

}