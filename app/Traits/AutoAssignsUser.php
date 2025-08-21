<?php

namespace App\Traits;

use Filament\Resources\Form;

trait AutoAssignsUser
{
    public static function assignUserField(Form $form): Form
    {
        return $form->schema(static::additionalFormFields());
    }
}
