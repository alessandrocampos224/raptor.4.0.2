<?php
/*
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Core\Support\Form\Fields;

use App\Core\Support\Form\Field;

class PasswordInput extends Field
{
    protected string $component = 'PasswordInput';
    protected ?string $type = 'password';
}
