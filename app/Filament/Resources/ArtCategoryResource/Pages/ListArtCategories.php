<?php

namespace App\Filament\Resources\ArtCategoryResource\Pages;

use App\Filament\Resources\ArtCategoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListArtCategories extends ListRecords
{
    protected static string $resource = ArtCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
