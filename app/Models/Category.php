<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends AbstractModel
{
    use HasFactory, HasUlids, SoftDeletes; 

    protected $guarded = ['id'];

    protected $casts = [
        'status' => CategoryStatus::class,
    ];
    
    /**
     * Obter os posts relacionados a esta categoria
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
} 