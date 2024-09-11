<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
 * @OA\Schema(
 *     schema="Tag",
 *     type="object",
 *     title="Tag",
 *     description="Модель тега",
 *     required={"id", "name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Идентификатор тега"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Название тега"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID пользователя, которому принадлежит тег"
 *     )
 * )
 */
    use HasFactory;

    protected $fillable = ['name'];

    public function notes()
    {
        return $this->belongsToMany(Note::class);
        
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
