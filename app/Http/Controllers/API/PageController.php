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
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @OA\Get(
     *     path="/api/page",
     *     tags={"Page"},
     *     summary="Get page by slug",
     *     operationId="index",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="slug",
     *                     type="string",
     *                     example="about"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Page retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 example={
     *                     "id": 1,
     *                     "slug": "about",
     *                        "title": "About Us",
     *                        "content": "<p>Content</p>",
     *                        "created_at": "2021-09-29T09:00:00.000000Z",
     *                        "updated_at": "2021-09-29T09:00:00.000000Z"
     *                 }
     *             )
     *         )
     *     )
     * )
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
            'message' => 'Page retrieved successfully'
        ]);
    }
}
