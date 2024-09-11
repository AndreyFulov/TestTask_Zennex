<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Note;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\NoteRequest;
use App\Http\Resources\NoteResource;
/**
 * @OA\Info(
 *     title="Zennex API Documentation",
 *     version="1.0.1",
 * )
 */
class NoteController extends Controller
{
    use AuthorizesRequests;
    /**
 * @OA\Get(
 *     path="/api/notes",
 *     summary="Получение списка заметок",
 *     description="Возвращает список всех заметок",
 *     tags={"Notes"},
 *     @OA\Response(
 *         response=200,
 *         description="Список заметок",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Note")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Заметки не найдены"
 *     )
 * )
 */
    public function index()
    {
        $notes = auth()->user()->notes()->with('tags')->get();
        return NoteResource::collection($notes);
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
        return new NoteResource($note->load('tags'));
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
    public function store(NoteRequest $request)
    {
        $validated = $request->validated();

        $note = auth()->user()->notes()->create($validated);

        if ($request->has('tags')) {
            $tags = Tag::whereIn('name', $request->tags)->get();
            $note->tags()->sync($tags);
        }

        return new NoteResource($note->load('tags'));
    }
     /**
 * @OA\Put(
 *     path="/notes/{id}",
 *     summary="Обновление заметки",
 *     description="Обновляет существующую заметку и синхронизирует теги.",
 *     tags={"Notes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID заметки для обновления",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "content"},
 *             @OA\Property(property="title", type="string", description="Заголовок заметки"),
 *             @OA\Property(property="content", type="string", description="Содержимое заметки"),
 *             @OA\Property(property="tags", type="array", description="Массив тегов",
 *                 @OA\Items(type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешное обновление заметки",
 *         @OA\JsonContent(ref="#/components/schemas/Note")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Заметка не найдена"
 *     )
 * )
 */
public function update(NoteRequest $request, Note $note)
{
     $this->authorize('update', $note);
        $validated = $request->validated();

        $note->update($validated);

        if ($request->has('tags')) {
            $tags = Tag::whereIn('name', $request->tags)->get();
            $note->tags()->sync($tags);
        }

        return new NoteResource($note->load('tags'));
}
 /**
     * @OA\Delete(
     *     path="/api/notes/{id}",
     *     tags={"Notes"},
     *     summary="Удалить заметку",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID заметки",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Заметка успешно удалена"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заметка не найдена"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Не авторизован"
     *     )
     * )
     */
    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();
        return response()->json(null, 204);
    }
/**
 * @OA\Get(
 *     path="/api/notes/search-by-tags",
 *     summary="Поиск заметок по тегам",
 *     description="Возвращает список заметок, соответствующих переданным тегам пользователю.",
 *     tags={"Notes"},
 *     @OA\Parameter(
 *         name="tags",
 *         in="query",
 *         description="Массив тегов для поиска заметок",
 *         required=true,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Список заметок текущего пользователя, соответствующих тегам",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Note")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Неверный запрос"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован"
 *     )
 * )
 */
    public function searchByTags(Request $request)
    {
        $tags = $request->input('tags');
        $userId = auth()->id();
        $notes = Note::where('user_id', $userId)
            ->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('name', $tags);
            })
            ->get();

        return NoteResource::collection($notes);
    }
}
