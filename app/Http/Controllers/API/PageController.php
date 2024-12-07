<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class PageController extends Controller
{

    /**
     * Get page by slug.
     *
     * @OA\Get(
     *     path="/api/page/{slug}",
     *     summary="Get page by slug",
     *     tags={"Pages"},
     *     @OA\Parameter(
     *         in="path",
     *         name="slug",
     *         description="Slug of the page",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page berhasil diambil",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Page berhasil diambil"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/PageResource"
     *             )
     *         )
     *     )
     * )
     *
     * @param string $slug
     * @return JsonResource
     */
    public function index(string $slug): JsonResource|JsonResponse
    {
        $page = Page::query()
                ->where('slug', $slug)
                ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page tidak ditemukan'
            ], 200);
        }

        return (new PageResource($page))->additional([
            'success' => true,
            'message' => 'Page berhasil diambil'
        ]);
    }
}
