<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditHomepageAuctions extends EditRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('auctions_title')->label('عنوان قسم المزادات'),
            Forms\Components\Textarea::make('auctions_subtitle')->label('وصف قسم المزادات')->rows(2),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ قسم المزادات';
    }
}
