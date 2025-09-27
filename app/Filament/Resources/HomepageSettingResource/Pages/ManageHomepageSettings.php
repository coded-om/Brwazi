<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;

class ManageHomepageSettings extends ManageRecords
{
    protected static string $resource = HomepageSettingResource::class;

    protected function canCreate(): bool
    {
        // Force single record management
        return \App\Models\HomepageSetting::count() === 0;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء الإعدادات')
                ->visible(fn() => \App\Models\HomepageSetting::count() === 0),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'لا توجد إعدادات بعد';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'قم بإنشاء أول إعدادات للصفحة الرئيسية لتعديل محتوى الصفحة.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء الإعدادات')
                ->visible(fn() => \App\Models\HomepageSetting::count() === 0),
        ];
    }
}
