<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/sales",
     *     summary="Get list of sales",
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         in="query",
     *         name="search",
     *         description="Search for a sale by name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="limit",
     *         description="Number of sales to return per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
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
     *                 example="Sale berhasil diambil"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/SaleResource"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(SaleRequest $request): JsonResource
    {
        $request->validated();

        $paginate = $request->limit ?? 10;
        $sales = Product::query()
                ->where($request->has('search'), function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%");
                })->paginate($paginate);

        return SaleResource::collection($sales)->additional([
            'success' => true,
            'message' => 'Sale berhasil diambil'
        ]);
    }

    /**
     * Get a sale by slug.
     *
     * @OA\Get(
     *     path="/api/sales/{slug}",
     *     summary="Get a sale by slug",
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         in="path",
     *         name="slug",
     *         description="Sale slug",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
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
     *                 example="Sale berhasil diambil"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/SaleResource"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $slug): JsonResource
    {
        $sale = Product::query()
                ->where('slug', $slug)
                ->first();

        return (new SaleResource($sale))->additional([
            'success' => true,
            'message' => 'Sale berhasil diambil'
        ]);
    }
}
