<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditHomepageHero extends EditRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('hero_text')->label('نص الهيرو')->rows(3),
            Forms\Components\FileUpload::make('hero_bg_image')->label('صورة الخلفية')
                ->disk('public')->directory('settings')->image()->visibility('public'),
            Forms\Components\FileUpload::make('hero_logo')->label('شعار')
                ->disk('public')->directory('settings')->image()->visibility('public'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ الهيرو';
    }
}
