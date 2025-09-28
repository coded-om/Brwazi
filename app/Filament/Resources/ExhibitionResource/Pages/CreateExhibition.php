<?php

namespace App\Filament\Resources\ExhibitionResource\Pages;

use App\Filament\Resources\ExhibitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExhibition extends CreateRecord
{
    protected static string $resource = ExhibitionResource::class;

    protected function afterCreate(): void
    {
        \App\Filament\Resources\ExhibitionResource::processImages($this->record);
    }
}
