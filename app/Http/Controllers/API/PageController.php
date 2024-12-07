<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class PageController extends Controller
{

    /**
     * Get page by slug.
     *
     * @OA\Get(
     *     path="/api/page",
     *     summary="Get page by slug",
     *     tags={"Page"},
     *     @OA\Parameter(
     *         description="Page slug",
     *         in="query",
     *         name="slug",
     *         required=true,
     *         example="kebijakan-privasi",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page successfully retrieved",
     *         @OA\JsonContent(ref="#/components/schemas/PageResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     )
     * )
     * @param Request $request
     * @return JsonResource
     */
    public function index(Request $request): JsonResource
    {
        $request->validate([
            'slug' => 'required|string'
        ]);

        $page = Page::query()
                ->where('slug', $request->slug)
                ->first();

        return (new PageResource($page))->additional([
            'success' => true,
            'message' => 'Page berhasil diambil'
        ]);
    }
}
