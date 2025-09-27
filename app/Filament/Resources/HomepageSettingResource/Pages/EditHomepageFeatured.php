<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditHomepageFeatured extends EditRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('featured_artist_title')->label('العنوان'),
            Forms\Components\Textarea::make('featured_artist_description')->label('الوصف')->rows(3),
            Forms\Components\FileUpload::make('featured_artist_image')->label('الصورة')
                ->disk('public')->directory('settings')->image()->visibility('public'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ الفنان المميز';
    }
}
