<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditHomepageEvents extends EditRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Repeater::make('events')->label('قائمة الفعاليات')->schema([
                Forms\Components\TextInput::make('title')->label('العنوان')->required(),
                Forms\Components\Textarea::make('description')->label('الوصف')->rows(2)->required(),
                Forms\Components\TextInput::make('day')->label('اليوم')->numeric()->minValue(1)->maxValue(31)->required(),
                Forms\Components\TextInput::make('month')->label('الشهر')->required(),
                Forms\Components\TextInput::make('link')->label('رابط (اختياري)'),
            ])->columns(2)->addActionLabel('إضافة فعالية')->collapsible()->reorderable(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ الفعاليات';
    }
}
