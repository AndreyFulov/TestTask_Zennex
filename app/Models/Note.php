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
 *         description="ID of the note"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the note"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Content of the note"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the user who created the note"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
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
