<?php

namespace App\Filament\Resources\WorkshopResource\Pages;

use App\Filament\Resources\WorkshopResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Workshop;

class EditWorkshop extends EditRecord
{
    protected static string $resource = WorkshopResource::class;

    protected function afterSave(): void
    {
        if ($this->record instanceof Workshop) {
            WorkshopResource::processImages($this->record);
        }
    }
}
