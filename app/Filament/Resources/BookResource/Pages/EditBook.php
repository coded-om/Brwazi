<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Models\Publisher;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Keep the publisher fixed to Ministry on updates as well
        $data['publisher_id'] = Publisher::query()
            ->where('name', 'وزارة الثقافة')
            ->value('id') ?? Publisher::query()->value('id');

        return $data;
    }
}
