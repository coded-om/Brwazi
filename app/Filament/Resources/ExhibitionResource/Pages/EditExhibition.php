<?php

namespace App\Filament\Resources\ExhibitionResource\Pages;

use App\Filament\Resources\ExhibitionResource;
use Filament\Resources\Pages\EditRecord;

class EditExhibition extends EditRecord
{
    protected static string $resource = ExhibitionResource::class;

    protected function afterSave(): void
    {
        \App\Filament\Resources\ExhibitionResource::processImages($this->record);
    }
}
