<?php

namespace App\Filament\Resources\ExhibitionResource\Pages;

use App\Filament\Resources\ExhibitionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListExhibitions extends ListRecords
{
    protected static string $resource = ExhibitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة معرض')
                ->modalHeading('إضافة معرض جديد')
                ->createAnother(false),
        ];
    }
}
