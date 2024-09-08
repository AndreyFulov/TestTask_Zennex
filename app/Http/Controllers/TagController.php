<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
 /**
     * @OA\Get(
     *     path="/api/tags",
     *     tags={"Tags"},
     *     summary="Get a list of tags",
     *     @OA\Response(
     *         response=200,
     *         description="List of tags"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags, 200);
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
        $request->validate([
            'name' => 'required|unique:tags,name',
        ]);

        $tag = Tag::create($request->only('name'));
        return response()->json($tag, 201);
    }
}
