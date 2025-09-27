<?php

namespace App\Filament\Resources\VerificationFormContentResource\Pages;

use App\Filament\Resources\VerificationFormContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVerificationFormContents extends ListRecords
{
    protected static string $resource = VerificationFormContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة نوع استمارة'),
        ];
    }
}
