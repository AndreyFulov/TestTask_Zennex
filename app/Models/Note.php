<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
/**
 * @OA\Schema(
 *     schema="Note",
 *     type="object",
 *     required={"title", "content", "user_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID заметки"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Название заметки"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Контент заметки"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         format="int64",
 *         description="ID пользователя создавшего заметку"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Метка даты"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Последнее обновление"
 *     )
 * )
 */
    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    
}
