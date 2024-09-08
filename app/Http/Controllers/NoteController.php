<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Note;

/**
 * @OA\Info(
 *     title="Laravel API Documentation",
 *     version="1.0.0",
 * )
 */
class NoteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/notes",
     *     summary="Получить все заметки пользователя",
     *     tags={"Notes"},
     *     @OA\Response(
     *         response=200,
     *         description="Список заметок"
     *     )
     * )
     */
    public function index()
    {
        return auth()->user()->notes()->with('tags')->get();
    }

    /**
     * @OA\Get(
     *     path="/api/notes/{id}",
     *     summary="Получить заметку по ID",
     *     tags={"Notes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заметка найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заметка не найдена"
     *     )
     * )
     */
    public function show(Note $note)
    {
        $this->authorize('view', $note);
        return $note->load('tags');
    }
    /**
     * @OA\Post(
     *     path="/api/notes",
     *     summary="Создать новую заметку",
     *     tags={"Notes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Новая заметка"),
     *             @OA\Property(property="content", type="string", example="Содержание заметки")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заметка создана",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'tags' => 'array'
        ]);

        $note = auth()->user()->notes()->create($validated);

        if ($request->has('tags')) {
            $tags = Tag::whereIn('name', $request->tags)->get();
            $note->tags()->sync($tags);
        }

        return response()->json($note->load('tags'), 201);
    }

     /**
     * @OA\Put(
     *     path="/api/notes/{id}",
     *     summary="Обновить заметку",
     *     tags={"Notes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Обновленный заголовок"),
     *             @OA\Property(property="content", type="string", example="Обновленное содержание")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заметка обновлена"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заметка не найдена"
     *     )
     * )
     */
    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'tags' => 'array'
        ]);

        $note->update($validated);

        if ($request->has('tags')) {
            $tags = Tag::whereIn('name', $request->tags)->get();
            $note->tags()->sync($tags);
        }

        return $note->load('tags');
    }
 /**
     * @OA\Delete(
     *     path="/api/notes/{id}",
     *     tags={"Notes"},
     *     summary="Delete a specific note",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the note",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Note deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Note not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();
        return response()->json(null, 204);
    }

    public function searchByTags(Request $request)
    {
        $tags = $request->input('tags');
        $notes = Note::whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('name', $tags);
        })->get();

        return response()->json($notes);
    }
}
