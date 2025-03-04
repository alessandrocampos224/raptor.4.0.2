<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Models;

use App\Core\Landlord\BelongsToTenants;
use App\Enums\AboutStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\SoftDeletes;


class About extends AbstractModel
{
    use HasFactory, HasUlids, SoftDeletes, BelongsToTenants; 

    protected $guarded = ['id'];

    protected $casts = [
        'status' => AboutStatus::class,
    ];
} 