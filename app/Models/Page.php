<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Models;

use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\SoftDeletes;


class Page extends AbstractModel
{
    use HasFactory, HasUlids, SoftDeletes; 

    protected $guarded = ['id'];

    protected $casts = [
        'config' => 'array',
        'status' => PageStatus::class,
    ];
} 