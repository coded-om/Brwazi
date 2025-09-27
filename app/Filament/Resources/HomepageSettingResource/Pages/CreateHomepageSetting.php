<?php

namespace App\Filament\Resources\HomepageSettingResource\Pages;

use App\Filament\Resources\HomepageSettingResource;
use App\Models\HomepageSetting;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;

class CreateHomepageSetting extends CreateRecord
{
    protected static string $resource = HomepageSettingResource::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Wizard::make([
                Forms\Components\Wizard\Step::make('الهيرو')->schema([
                    Forms\Components\Textarea::make('hero_text')->label('نص الهيرو')->rows(3),
                    Forms\Components\FileUpload::make('hero_bg_image')->label('صورة الخلفية')
                        ->disk('public')->directory('settings')->image()->visibility('public'),
                    Forms\Components\FileUpload::make('hero_logo')->label('شعار')
                        ->disk('public')->directory('settings')->image()->visibility('public'),
                ])->columns(2),
                Forms\Components\Wizard\Step::make('الفنان المميز')->schema([
                    Forms\Components\TextInput::make('featured_artist_title')->label('العنوان'),
                    Forms\Components\Textarea::make('featured_artist_description')->label('الوصف')->rows(3),
                    Forms\Components\FileUpload::make('featured_artist_image')->label('الصورة')
                        ->disk('public')->directory('settings')->image()->visibility('public'),
                ])->columns(2),
                Forms\Components\Wizard\Step::make('شرائح الفن')->schema([
                    Forms\Components\Repeater::make('art_slides')->label('الشرائح')->schema([
                        Forms\Components\TextInput::make('title')->label('العنوان')->required(),
                        Forms\Components\Textarea::make('description')->label('الوصف')->rows(2)->required(),
                        Forms\Components\FileUpload::make('image')->label('الصورة')
                            ->disk('public')->directory('settings/slides')->image()->visibility('public')->required(),
                    ])->columns(2)->addActionLabel('إضافة شريحة')->collapsible()->reorderable(),
                ]),
                Forms\Components\Wizard\Step::make('المزادات')->schema([
                    Forms\Components\TextInput::make('auctions_title')->label('عنوان قسم المزادات'),
                    Forms\Components\Textarea::make('auctions_subtitle')->label('وصف قسم المزادات')->rows(2),
                ]),
                Forms\Components\Wizard\Step::make('الفعاليات')->schema([
                    Forms\Components\Repeater::make('events')->label('قائمة الفعاليات')->schema([
                        Forms\Components\TextInput::make('title')->label('العنوان')->required(),
                        Forms\Components\Textarea::make('description')->label('الوصف')->rows(2)->required(),
                        Forms\Components\TextInput::make('day')->label('اليوم')->numeric()->minValue(1)->maxValue(31)->required(),
                        Forms\Components\TextInput::make('month')->label('الشهر')->required(),
                        Forms\Components\TextInput::make('link')->label('رابط (اختياري)'),
                    ])->columns(2)->addActionLabel('إضافة فعالية')->collapsible()->reorderable(),
                ]),
            ])->skippable(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Enforce single record: if exists, redirect to edit
        if (HomepageSetting::query()->exists()) {
            $record = HomepageSetting::query()->first();
            $this->redirect(static::getResource()::getUrl('edit', ['record' => $record]));
        }
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم إنشاء الإعدادات بنجاح';
    }
}
