<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditHomepageSetting extends EditRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ الإعدادات';
    }

    protected function getHeaderActions(): array
    {
        $id = $this->record?->getKey();
        return [
            Actions\Action::make('hero')->label('الهيرو')->url(static::getResource()::getUrl('edit-hero', ['record' => $id]))->button()->color('gray'),
            Actions\Action::make('featured')->label('الفنان المميز')->url(static::getResource()::getUrl('edit-featured', ['record' => $id]))->button()->color('gray'),
            Actions\Action::make('slides')->label('شرائح الفن')->url(static::getResource()::getUrl('edit-slides', ['record' => $id]))->button()->color('gray'),
            Actions\Action::make('auctions')->label('المزادات')->url(static::getResource()::getUrl('edit-auctions', ['record' => $id]))->button()->color('gray'),
            Actions\Action::make('events')->label('الفعاليات')->url(static::getResource()::getUrl('edit-events', ['record' => $id]))->button()->color('gray'),
        ];
    }
}
