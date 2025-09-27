<?php

namespace App\Filament\Resources\LiteratureWorkshopResource\Pages;

use App\Filament\Resources\LiteratureWorkshopResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLiteratureWorkshop extends CreateRecord
{
    protected static string $resource = LiteratureWorkshopResource::class;

    protected function afterCreate(): void
    {
        LiteratureWorkshopResource::processImages($this->record);
    }
}
