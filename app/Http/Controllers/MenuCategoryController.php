<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $menuCategories = MenuCategory::latest()->paginate(15);

        return view('menu-categories.index', compact('menuCategories'));
    }

    public function create()
    {
        return view('menu-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        MenuCategory::create($validated);

        return redirect()->route('menu-categories.index')->with('success', 'Menu category created successfully.');
    }

    public function show(MenuCategory $menuCategory)
    {
        $menuCategory->load('menuItems');

        return view('menu-categories.show', compact('menuCategory'));
    }

    public function edit(MenuCategory $menuCategory)
    {
        return view('menu-categories.edit', compact('menuCategory'));
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $menuCategory->update($validated);

        return redirect()->route('menu-categories.index')->with('success', 'Menu category updated successfully.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        $menuCategory->delete();

        return redirect()->route('menu-categories.index')->with('success', 'Menu category deleted successfully.');
    }
}
