<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pages\AboutPageResource;
use App\Http\Resources\Pages\AboutPageResourceCollection;
use App\Models\Pages\AboutPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    public function getAboutPage(): AboutPageResource|JsonResponse
    {
        $aboutPage = AboutPage::where(AboutPage::IS_ACTIVE, true)->first();

        if (!$aboutPage) {
            return response()->json(['message' => 'About page not found'], 404);
        }

        return new AboutPageResource($aboutPage);
    }

    public function getAllAboutPages(): AboutPageResourceCollection
    {
        $aboutPages = AboutPage::all();

        return new AboutPageResourceCollection($aboutPages);
    }

    public function createAboutPage(Request $request): AboutPageResource|JsonResponse
    {
        $validated = $request->validate([
            AboutPage::TITLE => 'required|string|max:255',
            AboutPage::CONTENT => 'required|string',
            AboutPage::IS_ACTIVE => 'required|boolean',
        ]);

        if ($validated[AboutPage::IS_ACTIVE]) {
            AboutPage::where(AboutPage::IS_ACTIVE, true)
                ->update([AboutPage::IS_ACTIVE => false]);
        }

        $aboutPage = AboutPage::create($validated);

        return new AboutPageResource($aboutPage);
    }

    public function updateAboutPage(Request $request, int $id): AboutPageResource|JsonResponse
    {
        $aboutPage = AboutPage::find($id);

        if (!$aboutPage) {
            return response()->json(['message' => 'About page not found'], 404);
        }

        $validated = $request->validate([
            AboutPage::TITLE => 'required|string|max:255',
            AboutPage::CONTENT => 'required|string',
            AboutPage::IS_ACTIVE => 'required|boolean',
        ]);

        if ($validated[AboutPage::IS_ACTIVE] && !$aboutPage->getIsActive()) {
            AboutPage::where(AboutPage::IS_ACTIVE, true)
                ->update([AboutPage::IS_ACTIVE => false]);
        }

        $aboutPage->update($validated);

        return new AboutPageResource($aboutPage);
    }

    public function deleteAboutPage(int $id): JsonResponse
    {
        $aboutPage = AboutPage::find($id);

        if (!$aboutPage) {
            return response()->json(['message' => 'About page not found'], 404);
        }

        $aboutPage->delete();

        return response()->json(['message' => 'About page deleted successfully']);
    }

    public function getAboutPageById(int $id): AboutPageResource|JsonResponse
    {
        $aboutPage = AboutPage::find($id);

        if (!$aboutPage) {
            return response()->json(['message' => 'About page not found'], 404);
        }

        return new AboutPageResource($aboutPage);
    }
}
