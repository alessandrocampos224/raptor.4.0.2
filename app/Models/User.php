<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Models;

use App\Models\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\User as Authenticatable;
use App\Models\Traits\HasAddresses;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUlids, Notifiable, HasRoles, HasFactory, HasAddresses;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'access', 
    ];

    protected $with = ['defaultAddress'];

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function getAccessAttribute()
    {
        return array_keys($this->roles()->pluck('id', 'id')->toArray());
    }
 
    /**
     * @return string | false
     */
    protected function slugTo()
    {
        return false;
    }
}
