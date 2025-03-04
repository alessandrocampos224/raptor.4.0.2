<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Enums;

enum DefaultStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Rascunho',
            self::PUBLISHED => 'Publicado',
        };
    }
}
