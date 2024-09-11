<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Http\Resources\TagResource;

class TagController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/tags",
 *     summary="Получение списка тегов",
 *     description="Возвращает список всех тегов",
 *     tags={"Tags"},
 *     @OA\Response(
 *         response=200,
 *         description="Список тегов",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Tag")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Теги не найдены"
 *     )
 * )
 */
    public function index()
    {
        $tags = auth()->user()->tags()->get();
        return TagResource::collection($tags);
    }
     /**
     * @OA\Post(
     *     path="/api/tags",
     *     tags={"Tags"},
     *     summary="Create a new tag",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Tag Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $tag = auth()->user()->tags()->create($validatedData);
        return new TagResource($tag);
    }
}
