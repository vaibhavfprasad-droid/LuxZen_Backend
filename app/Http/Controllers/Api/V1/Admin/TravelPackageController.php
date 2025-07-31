<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TravelPackageController extends Controller
{
    /**
     * Store a newly created travel package in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            // Image Validation: must be an image, specific types, max 2MB
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Store the image and get its path
        // The 'packages' argument is the sub-directory inside 'storage/app/public'
        $imagePath = $request->file('image')->store('packages', 'public');
        $validated['image_url'] = $imagePath;

        $package = TravelPackage::create($validated);

        return response()->json($package, 201);
    }

    /**
     * Update the specified travel package in storage.
     */
    public function update(Request $request, TravelPackage $travelPackage)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'destination' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after:start_date'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($travelPackage->image_url) {
                Storage::disk('public')->delete($travelPackage->image_url);
            }
            // Store the new image and update the path
            $imagePath = $request->file('image')->store('packages', 'public');
            $validated['image_url'] = $imagePath;
        }

        $travelPackage->update($validated);

        return response()->json($travelPackage);
    }

    /**
     * Remove the specified travel package from storage.
     */
    public function destroy(TravelPackage $travelPackage)
    {
        // Delete the associated image from storage first
        if ($travelPackage->image_url) {
            Storage::disk('public')->delete($travelPackage->image_url);
        }

        $travelPackage->delete();

        // Return a 204 No Content response
        return response()->noContent();
    }
}