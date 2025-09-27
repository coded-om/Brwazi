<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Publisher;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Force publisher to Ministry (or first publisher as fallback) regardless of client input
        $data['publisher_id'] = Publisher::query()
            ->where('name', 'وزارة الثقافة')
            ->value('id') ?? Publisher::query()->value('id');

        return $data;
    }
}
