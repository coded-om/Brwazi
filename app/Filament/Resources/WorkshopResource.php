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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
            Forms\Components\FileUpload::make('presenter_avatar_path')
                ->label('صورة المقدم')
                ->image()
                ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => 'raw-' . uniqid() . '.' . $file->getClientOriginalExtension())
                ->directory('workshops/presenters/raw')
                ->disk('public')
                ->maxSize(8192),
            Forms\Components\TextInput::make('art_type')->label('نوع الفن'),
            Forms\Components\DateTimePicker::make('starts_at')->label('تاريخ ووقت البداية')->required()->seconds(false),
            Forms\Components\TextInput::make('duration_minutes')->label('المدة بالدقائق')->numeric()->minValue(15)->maxValue(1440),
            Forms\Components\TextInput::make('location')->label('الموقع / المنصة'),
            Forms\Components\Textarea::make('short_description')->label('وصف مختصر')->rows(3),
            Forms\Components\TextInput::make('external_apply_url')->label('رابط خارجي للتسجيل')->url(),
            Forms\Components\FileUpload::make('cover_image_path')
                ->label('صورة الغلاف')
                ->image()
                ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => 'raw-' . uniqid() . '.' . $file->getClientOriginalExtension())
                ->directory('workshops/covers/raw')
                ->disk('public')
                ->maxSize(10240),
            Forms\Components\Toggle::make('is_published')->label('منشور')->default(false),
            Forms\Components\Toggle::make('is_approved')->label('موافَق عليه')->default(false),
            Forms\Components\Select::make('submitted_by_user_id')->relationship('submitter', 'email')->label('مقدم بواسطة (مستخدم)')->searchable()->preload(),
        ])
            ->columns(2)
            ->model(Workshop::class)
            ->saveRelationshipsUsing(function ($record, $form) {
                // process raw images if present
                $imageService = app(\App\Services\ImageService::class);
                $dirty = false;
                if ($record->cover_image_path && str_contains($record->cover_image_path, '/raw/')) {
                    $rawPath = $record->cover_image_path; // storage path relative
                    $full = storage_path('app/public/' . $rawPath);
                    if (is_file($full)) {
                        $uploaded = new \Illuminate\Http\UploadedFile($full, basename($full));
                        $processed = $imageService->coverWideWebp($uploaded, 1280, 'workshops/covers');
                        $record->cover_image_path = $processed['path'];
                        $dirty = true;
                    }
                }
                if ($record->presenter_avatar_path && str_contains($record->presenter_avatar_path, '/raw/')) {
                    $rawPath = $record->presenter_avatar_path;
                    $full = storage_path('app/public/' . $rawPath);
                    if (is_file($full)) {
                        $uploaded = new \Illuminate\Http\UploadedFile($full, basename($full));
                        $processed = $imageService->uploadAndCrop($uploaded, 'workshops/presenters', 400, 400);
                        $record->presenter_avatar_path = $processed['path'];
                        $dirty = true;
                    }
                }
                if ($dirty) {
                    $record->save();
                }
            });
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image_path')
                ->label('غلاف')
                ->getStateUsing(function (Workshop $record) {
                    return $record->cover_image_path
                        ? asset('storage/' . ltrim($record->cover_image_path, '/'))
                        : null;
                })
                ->square(),
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
                        ->visible(fn(Workshop $record) => !$record->is_approved)
                        ->action(function (Workshop $record) {
                            $record->update(['is_approved' => true]);
                        }),
                    Tables\Actions\Action::make('publish')
                        ->label('نشر/إلغاء')
                        ->icon('heroicon-o-eye')
                        ->action(function (Workshop $record) {
                            $record->update(['is_published' => !$record->is_published]);
                        }),
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

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\WorkshopResource\RelationManagers\RegistrationsRelationManager::class,
        ];
    }
}
