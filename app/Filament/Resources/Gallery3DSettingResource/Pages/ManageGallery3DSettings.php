<?php

namespace App\Filament\Resources\Gallery3DSettingResource\Pages;

use App\Filament\Resources\Gallery3DSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGallery3DSettings extends ManageRecords
{
    protected static string $resource = Gallery3DSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء إعدادات')
                ->using(function ($data, $model) {
                    // Ensure single record: if one exists, redirect to edit it instead of creating multiple
                    $existing = \App\Models\Gallery3DSetting::query()->first();
                    if ($existing) {
                        return $existing;
                    }
                    return $model::create($data);
                }),
        ];
    }
}
