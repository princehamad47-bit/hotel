<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with('menuCategory')->latest()->paginate(15);

        return view('menu-items.index', compact('menuItems'));
    }

    public function create()
    {
        $menuCategories = MenuCategory::where('is_active', true)->orderBy('name')->get();

        return view('menu-items.create', compact('menuCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_category_id' => ['required', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->has('is_available');

        MenuItem::create($validated);

        return redirect()->route('menu-items.index')->with('success', 'Menu item created successfully.');
    }

    public function show(MenuItem $menuItem)
    {
        $menuItem->load('menuCategory');

        return view('menu-items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $menuCategories = MenuCategory::where('is_active', true)->orderBy('name')->get();

        return view('menu-items.edit', compact('menuItem', 'menuCategories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'menu_category_id' => ['required', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->has('is_available');

        $menuItem->update($validated);

        return redirect()->route('menu-items.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('menu-items.index')->with('success', 'Menu item deleted successfully.');
    }
}
