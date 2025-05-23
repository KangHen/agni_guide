<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoricSiteResource;
use App\Models\HistoricSite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class HistoricSiteController extends Controller
{
    private int $limit = 100;

    /**
     * Get historic sites.
     *
     * @OA\Get(
     *     path="/api/historic-sites",
     *     summary="Get historic sites",
     *     tags={"Historic Sites"},
     *     @OA\Parameter(
     *         description="Category ID",
     *         in="query",
     *         name="category",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Search query",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historic sites successfully retrieved",
     *         @OA\JsonContent(ref="#/components/schemas/HistoricSiteResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Historic sites not found"
     *     )
     * )
     */
    public function index(Request $request): JsonResource
    {
        $longitude  = $request->longitude ?? config('app.default_longitude');
        $latitude   = $request->latitude ?? config('app.default_latitude');
        $radius     = $request->radius ?? 25;

        $sql = "id, name, category_id, latitude, longitude, ( 6371 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance ";
        $historicSites = HistoricSite::with([
                'category' => fn ($q) => $q->select('id', 'name')
            ])
            ->publish()
            ->selectRaw($sql)
            ->when($request->has('categories'), fn ($q) => $q->whereIn('category_id', explode(',',$request->categories)))
            ->when($request->has('search'), fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->having('distance', '<', $radius)
            ->paginate($this->limit);

        return HistoricSiteResource::collection($historicSites)->additional([
            'success' => true,
            'message' => 'Situs berhasil diambil',
            'categories' => $request->categories
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResource
     */
    public function showcase(Request $request): JsonResource
    {
        $limit = $request->limit ?? $this->limit;
        $historicSites = HistoricSite::with([
                'category' => fn ($q) => $q->select('id', 'name')
            ])
            ->publish()
            ->when($request->pinned, fn ($q) => $q->where('is_pinned', true))
            ->when($request->pupuler, fn ($q) => $q->orderBy('read_count', 'desc'))
            ->when($request->explore, function ($q) use ($request) {
                return $q->where('is_pinned', false)
                         ->whereNotIn('id', explode(',', $request->exclude));
            })
            ->paginate($limit);

        return HistoricSiteResource::collection($historicSites)->additional([
            'success' => true,
            'message' => 'Situs berhasil diambil'
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResource
    {
        $historicSite = HistoricSite::with('category', 'user')
            ->where('id', $id)
            ->first();

        if ($historicSite) {
            $historicSite->read_count += 1;
            $historicSite->save();
        }

        return (new HistoricSiteResource($historicSite))->additional([
            'success' => true,
            'message' => 'Situs berhasil diambil',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
