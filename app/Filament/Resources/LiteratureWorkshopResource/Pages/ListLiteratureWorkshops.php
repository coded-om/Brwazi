<?php

namespace App\Filament\Resources\LiteratureWorkshopResource\Pages;

use App\Filament\Resources\LiteratureWorkshopResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListLiteratureWorkshops extends ListRecords
{
    protected static string $resource = LiteratureWorkshopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
