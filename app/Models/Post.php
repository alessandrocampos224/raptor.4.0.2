<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends AbstractModel
{
    use HasFactory, HasUlids, SoftDeletes; 

    protected $guarded = ['id'];

    protected $casts = [
        'status' => PostStatus::class,
    ];
    
    /**
     * Obter a categoria a que este post pertence
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
} 