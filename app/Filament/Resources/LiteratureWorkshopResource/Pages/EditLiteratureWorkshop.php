<?php

namespace App\Filament\Resources\LiteratureWorkshopResource\Pages;

use App\Filament\Resources\LiteratureWorkshopResource;
use Filament\Resources\Pages\EditRecord;

class EditLiteratureWorkshop extends EditRecord
{
    protected static string $resource = LiteratureWorkshopResource::class;

    protected function afterSave(): void
    {
        LiteratureWorkshopResource::processImages($this->record);
    }
}
