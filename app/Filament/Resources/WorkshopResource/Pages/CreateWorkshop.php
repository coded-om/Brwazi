<?php

namespace App\Filament\Resources\WorkshopResource\Pages;

use App\Filament\Resources\WorkshopResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Workshop;

class CreateWorkshop extends CreateRecord
{
    protected static string $resource = WorkshopResource::class;

    protected function afterCreate(): void
    {
        if ($this->record instanceof Workshop) {
            WorkshopResource::processImages($this->record);
        }
    }
}
