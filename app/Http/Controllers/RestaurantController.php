<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function index(Request $request): View
    {
        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('restaurants.index', compact('restaurants'));
    }

    public function updateImage(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $validated = $request->validate([
            'logo_url' => ['nullable', 'url'],
            'logo_file' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('restaurants', 'public');
            $restaurant->logo = $path;
        } elseif (!empty($validated['logo_url'])) {
            $restaurant->logo = $validated['logo_url'];
        }

        $restaurant->save();

        return back()->with('status', 'Image updated for ' . $restaurant->name);
    }
}


