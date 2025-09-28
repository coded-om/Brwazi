<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExhibitionResource\Pages;
use App\Models\Exhibition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExhibitionResource extends Resource
{
    protected static ?string $model = Exhibition::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'الفعاليات';
    protected static ?string $modelLabel = 'معرض';
    protected static ?string $pluralModelLabel = 'المعارض';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('الأساسيات')->schema([
                Forms\Components\TextInput::make('title')->label('العنوان')->required()->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        $set('slug', Str::slug((string) $state));
                    }),
                Forms\Components\Hidden::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('short_description')->label('وصف مختصر')->rows(2)->maxLength(300),
                Forms\Components\Textarea::make('description')->label('الوصف التفصيلي')->rows(6)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('المكان')->schema([
                Forms\Components\TextInput::make('country')->label('الدولة'),
                Forms\Components\TextInput::make('city')->label('المدينة'),
                Forms\Components\TextInput::make('address')->label('العنوان')->columnSpanFull(),
                Forms\Components\TextInput::make('latitude')->label('Latitude')->numeric()->rule('between:-90,90'),
                Forms\Components\TextInput::make('longitude')->label('Longitude')->numeric()->rule('between:-180,180'),
                Forms\Components\Field::make('map_picker')
                    ->label('تحديد الإحداثيات على الخريطة')
                    ->columnSpanFull()
                    ->dehydrated(false)
                    ->view('filament.components.exhibition-map-picker'),
            ])->columns(2),

            Forms\Components\Section::make('المواعيد')->schema([
                Forms\Components\DateTimePicker::make('starts_at')->label('تاريخ البداية')->seconds(false),
                Forms\Components\DateTimePicker::make('ends_at')->label('تاريخ النهاية')->seconds(false),
            ])->columns(2),

            Forms\Components\Section::make('التواصل')->schema([
                Forms\Components\TextInput::make('website_url')->label('الموقع')->url()->nullable(),
                Forms\Components\TextInput::make('contact_email')->label('البريد الإلكتروني')->email()->nullable(),
                Forms\Components\TextInput::make('contact_phone')->label('الهاتف')->nullable(),
            ])->columns(3),

            Forms\Components\Section::make('الوسائط')->schema([
                Forms\Components\FileUpload::make('cover_image_path')->label('صورة الغلاف')
                    ->image()
                    ->directory('exhibitions/covers/raw')
                    ->disk('public')
                    ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => 'raw-' . uniqid() . '.' . $file->getClientOriginalExtension())
                    ->imageEditor()
                    ->maxSize(10240),
            ]),

            Forms\Components\Section::make('النشر')->schema([
                Forms\Components\Toggle::make('is_published')->label('منشور')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('مميز')->default(false),
            ])->columns(2),
        ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image_path')
                ->label('غلاف')
                ->getStateUsing(fn(Exhibition $r) => $r->cover_image_path ? asset('storage/' . ltrim($r->cover_image_path, '/')) : null)
                ->square(),
            Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('city')->label('المدينة')->toggleable(),
            Tables\Columns\TextColumn::make('country')->label('الدولة')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\IconColumn::make('is_published')->label('منشور')->boolean(),
            Tables\Columns\IconColumn::make('is_featured')->label('مميز')->boolean(),
            Tables\Columns\TextColumn::make('starts_at')->label('البداية')->dateTime('Y-m-d'),
            Tables\Columns\TextColumn::make('ends_at')->label('النهاية')->dateTime('Y-m-d')->toggleable(),
        ])->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('النشر'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('مميز'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExhibitions::route('/'),
            'create' => Pages\CreateExhibition::route('/create'),
            'edit' => Pages\EditExhibition::route('/{record}/edit'),
        ];
    }

    /**
     * Process raw uploaded cover image converting to final processed version
     * (placeholder — integrate with ImageService if desired later).
     */
    public static function processImages(Exhibition $record): void
    {
        // If using an ImageService pattern similar to WorkshopResource, implement here.
        if ($record->cover_image_path && str_contains($record->cover_image_path, '/raw/')) {
            if (class_exists(\App\Services\ImageService::class)) {
                $service = app(\App\Services\ImageService::class);
                $full = storage_path('app/public/' . $record->cover_image_path);
                if (is_file($full)) {
                    $uploaded = new \Illuminate\Http\UploadedFile($full, basename($full));
                    // Reuse a generic coverWideWebp if available otherwise leave as-is
                    if (method_exists($service, 'coverWideWebp')) {
                        $processed = $service->coverWideWebp($uploaded, 1280, 'exhibitions/covers');
                        if (!empty($processed['path'])) {
                            $record->cover_image_path = $processed['path'];
                            $record->save();
                        }
                    }
                }
            }
        }
    }
}
