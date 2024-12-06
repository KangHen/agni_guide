<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class CategoryController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Category"},
     *     summary="Get all categories",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             (example={
     *              success:true,
     *              data"":[{
     *                  id:1,name:"Category 1",
     *                  created_at:"2021-09-29T09:00:00.000000Z",
     *                  updated_at:"2021-09-29T09:00:00.000000Z"
     *             }]})
     *        )
     *    )
     * )
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
