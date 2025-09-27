<?php

namespace App\Filament\Resources\VerificationFormContentResource\Pages;

use App\Filament\Resources\VerificationFormContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerificationFormContent extends EditRecord
{
    protected static string $resource = VerificationFormContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
