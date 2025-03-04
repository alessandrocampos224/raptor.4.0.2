<?php

namespace App\Enums;

enum AddressStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Rascunho',
            self::PUBLISHED => 'Publicado'
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'green'
        };
    }
} 