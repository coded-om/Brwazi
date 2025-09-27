<?php

namespace App\Filament\Resources\TaglineResource\Pages;

use App\Filament\Resources\TaglineResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class ListTaglines extends ListRecords
{
    protected static string $resource = TaglineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة تخصص'),
            Actions\Action::make('clearCache')
                ->label('تحديث القائمة المخزنة')
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    Cache::forget('taglines.active.list');
                    Notification::make()
                        ->title('تم تحديث الكاش بنجاح')
                        ->success()
                        ->send();
                }),
        ];
    }
}
