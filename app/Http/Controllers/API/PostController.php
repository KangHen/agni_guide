<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostsRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class PostController extends Controller
{
    /**
     * Get list of posts.
     *
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get list of posts",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         in="query",
     *         name="search",
     *         description="Search for a post by title",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="limit",
     *         description="Number of posts to return per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post berhasil diambil",
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
     *                 example="Post berhasil diambil"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/PostResource"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(PostsRequest $request): JsonResource|JsonResponse
    {
        $perPage = $request->limit ?? 10;

        $posts = Post::publish()
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%");
                })
                ->latest()
                ->paginate($perPage);

        return PostResource::collection($posts)->additional([
            'success' => true,
            'message' => 'Post berhasil diambil'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{slug}",
     *     tags={"Post"},
     *     summary="Get a post by slug",
     *     description="Get a post by slug",
     *     @OA\Parameter(
     *         description="Post slug",
     *         in="path",
     *         name="slug",
     *         required=true,
     *         example="hello-world",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Failed response"
     *     )
     * )
     */
    public function show(string $slug): JsonResource|JsonResponse
    {
        $post = Post::with('user')
                ->where('slug', $slug)
                ->first();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post tidak ditemukan'
            ], 200);
        }

        $post->read_count += 1;
        $post->save();

        return (new PostResource($post))->additional([
            'success' => true,
            'message' => 'Post berhasil diambil'
        ]);
    }
}
