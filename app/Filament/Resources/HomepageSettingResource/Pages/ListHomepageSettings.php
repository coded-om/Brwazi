<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use App\Models\HomepageSetting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomepageSettings extends ListRecords
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء الإعدادات')
                ->visible(fn() => HomepageSetting::count() === 0),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        $count = HomepageSetting::count();
        if ($count === 0) {
            $this->redirect(static::getResource()::getUrl('create'));
            return;
        }

        if ($count === 1) {
            $record = HomepageSetting::query()->first();
            $this->redirect(static::getResource()::getUrl('edit', ['record' => $record]));
        }
    }

    public function getTitle(): string
    {
        return 'إعدادات الصفحة الرئيسية';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'لا توجد إعدادات بعد';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'سيتم تحويلك تلقائياً لإنشاء أول إعدادات.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إنشاء الإعدادات'),
        ];
    }
}
