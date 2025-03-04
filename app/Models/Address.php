<?php

namespace App\Models;

use App\Enums\AddressStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Address extends Model
{
    use HasUlids, SoftDeletes;
    
    protected $fillable = [
        'name',
        'recipient',
        'phone',
        'zip_code',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'is_default',
        'status'
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
        'status' => AddressStatus::class
    ];

    // Relationships
    public function addressable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', AddressStatus::PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', AddressStatus::DRAFT);
    }

    // Mutators & Accessors
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            '%s, %s%s - %s, %s/%s',
            $this->street,
            $this->number,
            $this->complement ? ' ' . $this->complement : '',
            $this->city,
            $this->state,
            $this->zip_code
        );
    }
} 