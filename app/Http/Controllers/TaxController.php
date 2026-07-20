<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::latest()->paginate(15);

        return view('taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        Tax::create($validated);

        return redirect()
            ->route('taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    public function show(Tax $tax)
    {
        $tax->load('reservationTaxes.reservation');

        return view('taxes.show', compact('tax'));
    }

    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $tax->update($validated);

        return redirect()
            ->route('taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admin can delete taxes.');
        }

        $tax->delete();

        return redirect()
            ->route('taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
