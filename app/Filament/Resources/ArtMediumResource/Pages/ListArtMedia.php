<?php

namespace App\Filament\Resources\ArtMediumResource\Pages;

use App\Filament\Resources\ArtMediumResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListArtMedia extends ListRecords
{
    protected static string $resource = ArtMediumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
