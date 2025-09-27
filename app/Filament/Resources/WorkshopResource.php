<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkshopResource\Pages;
use App\Models\Workshop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WorkshopResource extends Resource
{
    protected static ?string $model = Workshop::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'الفعاليات';
    protected static ?string $modelLabel = 'ورشة';
    protected static ?string $pluralModelLabel = 'الورشات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('العنوان')->required()->live(onBlur: true)
                ->afterStateUpdated(function (Forms\Set $set, $state) {
                    $set('slug', Str::slug((string) $state));
                }),
            Forms\Components\Hidden::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('presenter_name')->label('اسم المقدم')->required(),
            Forms\Components\Textarea::make('presenter_bio')->label('نبذة عن المقدم')->rows(3),
            Forms\Components\FileUpload::make('presenter_avatar_path')->label('صورة المقدم')->image()->directory('workshops/presenters')->disk('public')->maxSize(8192),
            Forms\Components\TextInput::make('art_type')->label('نوع الفن'),
            Forms\Components\DateTimePicker::make('starts_at')->label('تاريخ ووقت البداية')->required()->seconds(false),
            Forms\Components\TextInput::make('duration_minutes')->label('المدة بالدقائق')->numeric()->minValue(15)->maxValue(1440),
            Forms\Components\TextInput::make('location')->label('الموقع / المنصة'),
            Forms\Components\Textarea::make('short_description')->label('وصف مختصر')->rows(3),
            Forms\Components\TextInput::make('external_apply_url')->label('رابط خارجي للتسجيل')->url(),
            Forms\Components\FileUpload::make('cover_image_path')->label('صورة الغلاف')->image()->directory('workshops/covers')->disk('public')->maxSize(10240),
            Forms\Components\Toggle::make('is_published')->label('منشور')->default(false),
            Forms\Components\Toggle::make('is_approved')->label('موافَق عليه')->default(false),
            Forms\Components\Select::make('submitted_by_user_id')->relationship('submitter', 'email')->label('مقدم بواسطة (مستخدم)')->searchable()->preload(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image_path')->label('غلاف')->getStateUsing(fn($r) => $r->cover_image_path ? asset('storage/' . ltrim($r->cover_image_path, '/')) : null)->square(),
            Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('presenter_name')->label('المقدم')->searchable(),
            Tables\Columns\IconColumn::make('is_approved')->label('موافقة')->boolean(),
            Tables\Columns\IconColumn::make('is_published')->label('منشور')->boolean(),
            Tables\Columns\TextColumn::make('starts_at')->label('البداية')->dateTime('Y-m-d H:i'),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_approved')->label('الموافقة'),
            Tables\Filters\TernaryFilter::make('is_published')->label('النشر'),
        ])->actions([
            Tables\Actions\Action::make('approve')
                ->label('اعتماد')
                ->icon('heroicon-o-check')
                ->visible(fn(Workshop $record) => ! $record->is_approved)
                ->action(function (Workshop $record) { $record->update(['is_approved' => true]); }),
            Tables\Actions\Action::make('publish')
                ->label('نشر/إلغاء')
                ->icon('heroicon-o-eye')
                ->action(function (Workshop $record) { $record->update(['is_published' => ! $record->is_published]); }),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkshops::route('/'),
            'create' => Pages\CreateWorkshop::route('/create'),
            'edit' => Pages\EditWorkshop::route('/{record}/edit'),
        ];
    }
}
