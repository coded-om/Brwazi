<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditHomepageSlides extends EditRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Repeater::make('art_slides')->label('الشرائح')->schema([
                Forms\Components\TextInput::make('title')->label('العنوان')->required(),
                Forms\Components\Textarea::make('description')->label('الوصف')->rows(2)->required(),
                Forms\Components\FileUpload::make('image')->label('الصورة')
                    ->disk('public')->directory('settings/slides')->image()->visibility('public')->required(),
            ])->columns(2)->addActionLabel('إضافة شريحة')->collapsible()->reorderable(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ الشرائح';
    }
}
