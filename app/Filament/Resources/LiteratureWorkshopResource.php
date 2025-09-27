<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiteratureWorkshopResource\Pages;
use App\Models\LiteratureWorkshop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LiteratureWorkshopResource extends Resource
{
    protected static ?string $model = LiteratureWorkshop::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'الفعاليات';
    protected static ?string $modelLabel = 'ورشة أدبية';
    protected static ?string $pluralModelLabel = 'الورشات الأدبية';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('العنوان')->required()->live(onBlur: true)
                ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('slug', Str::slug((string) $state))),
            Forms\Components\Hidden::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('presenter_name')->label('اسم المقدم')->required(),
            Forms\Components\Textarea::make('presenter_bio')->label('نبذة عن المقدم')->rows(3),
            Forms\Components\FileUpload::make('presenter_avatar_path')
                ->label('صورة المقدم')
                ->image()
                ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => 'raw-' . uniqid() . '.' . $file->getClientOriginalExtension())
                ->directory('literature_workshops/presenters/raw')
                ->disk('public')
                ->maxSize(8192),
            Forms\Components\TextInput::make('genre')->label('التصنيف'),
            Forms\Components\DateTimePicker::make('starts_at')->label('تاريخ ووقت البداية')->required()->seconds(false),
            Forms\Components\TextInput::make('duration_minutes')->label('المدة بالدقائق')->numeric()->minValue(15)->maxValue(1440),
            Forms\Components\TextInput::make('capacity')->label('السعة القصوى')->numeric()->minValue(1)->maxValue(10000)->helperText('اتركه فارغاً لعدد غير محدود'),
            Forms\Components\TextInput::make('location')->label('الموقع / المنصة'),
            Forms\Components\Textarea::make('short_description')->label('وصف مختصر')->rows(3),
            Forms\Components\TextInput::make('external_apply_url')->label('رابط خارجي للتسجيل')->url(),
            Forms\Components\FileUpload::make('cover_image_path')
                ->label('صورة الغلاف')
                ->image()
                ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => 'raw-' . uniqid() . '.' . $file->getClientOriginalExtension())
                ->directory('literature_workshops/covers/raw')
                ->disk('public')
                ->maxSize(10240),
            Forms\Components\Toggle::make('is_published')->label('منشور')->default(false),
            Forms\Components\Toggle::make('is_approved')->label('موافَق عليه')->default(false),
            Forms\Components\Select::make('submitted_by_user_id')->relationship('submitter', 'email')->label('مقدم بواسطة (مستخدم)')->searchable()->preload(),
        ])->columns(2)->model(LiteratureWorkshop::class);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image_path')
                ->label('غلاف')
                ->getStateUsing(fn(LiteratureWorkshop $r) => $r->cover_image_path ? asset('storage/' . ltrim($r->cover_image_path, '/')) : null)
                ->square(),
            Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('presenter_name')->label('المقدم')->searchable(),
            Tables\Columns\TextColumn::make('registrations_count')->label('المسجلون')->badge()->sortable()->alignCenter()
                ->color(fn(LiteratureWorkshop $r) => ($r->capacity && $r->registrations_count >= $r->capacity) ? 'danger' : 'success')
                ->formatStateUsing(fn(LiteratureWorkshop $r) => $r->capacity ? ($r->registrations_count . ' / ' . $r->capacity) : $r->registrations_count),
            Tables\Columns\IconColumn::make('is_approved')->label('موافقة')->boolean(),
            Tables\Columns\IconColumn::make('is_published')->label('منشور')->boolean(),
            Tables\Columns\TextColumn::make('starts_at')->label('البداية')->dateTime('Y-m-d H:i'),
        ])->filters([
                    Tables\Filters\TernaryFilter::make('is_approved')->label('الموافقة'),
                    Tables\Filters\TernaryFilter::make('is_published')->label('النشر'),
                ])->actions([
                    Tables\Actions\Action::make('approve')->label('اعتماد')->icon('heroicon-o-check')
                        ->visible(fn(LiteratureWorkshop $r) => !$r->is_approved)
                        ->action(fn(LiteratureWorkshop $r) => $r->update(['is_approved' => true])),
                    Tables\Actions\Action::make('publish')->label('نشر/إلغاء')->icon('heroicon-o-eye')
                        ->disabled(fn(LiteratureWorkshop $r) => ($r->capacity && $r->registrations_count >= $r->capacity) && !$r->is_published)
                        ->tooltip(fn(LiteratureWorkshop $r) => ($r->capacity && $r->registrations_count >= $r->capacity && !$r->is_published) ? 'ممتلئة - لا يمكن نشرها' : null)
                        ->action(function (LiteratureWorkshop $r) {
                            if ($r->capacity && $r->registrations()->count() >= $r->capacity && !$r->is_published)
                                return;
                            $r->update(['is_published' => !$r->is_published]);
                        }),
                    Tables\Actions\Action::make('registrations')->label('المسجلون')->icon('heroicon-o-users')
                        ->modalHeading('قائمة المسجلين')
                        ->modalWidth('3xl')
                        ->modalContent(fn(LiteratureWorkshop $r) => view('admin.partials.workshop-registrations-list', [
                            'workshop' => $r,
                            'registrations' => $r->registrations()->latest()->get(),
                        ]))
                        ->visible(fn(LiteratureWorkshop $r) => $r->registrations()->exists()),
                    Tables\Actions\Action::make('export_csv')->label('تصدير CSV')->icon('heroicon-o-arrow-down-tray')
                        ->visible(fn(LiteratureWorkshop $r) => $r->registrations()->exists())
                        ->action(function (LiteratureWorkshop $r) {
                            $filename = 'literature-workshop-' . $r->id . '-registrations.csv';
                            $headers = [
                                'Content-Type' => 'text/csv; charset=UTF-8',
                                'Content-Disposition' => 'attachment; filename=' . $filename,
                            ];
                            return response()->streamDownload(function () use ($r) {
                                $out = fopen('php://output', 'w');
                                fwrite($out, "\xEF\xBB\xBF");
                                fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'WhatsApp', 'Registered At']);
                                $r->registrations()->orderBy('id')->chunk(500, function ($chunk) use ($out) {
                                    foreach ($chunk as $reg) {
                                        fputcsv($out, [
                                            $reg->id,
                                            $reg->name,
                                            $reg->email,
                                            $reg->phone,
                                            $reg->whatsapp_phone,
                                            $reg->created_at->toDateTimeString(),
                                        ]);
                                    }
                                });
                                fclose($out);
                            }, $filename, $headers);
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
            'index' => Pages\ListLiteratureWorkshops::route('/'),
            'create' => Pages\CreateLiteratureWorkshop::route('/create'),
            'edit' => Pages\EditLiteratureWorkshop::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            LiteratureWorkshopResource\RelationManagers\RegistrationsRelationManager::class,
        ];
    }

    public static function processImages(LiteratureWorkshop $record): void
    {
        $imageService = app(\App\Services\ImageService::class);
        $dirty = false;
        if ($record->cover_image_path && str_contains($record->cover_image_path, '/raw/')) {
            $full = storage_path('app/public/' . $record->cover_image_path);
            if (is_file($full)) {
                $uploaded = new \Illuminate\Http\UploadedFile($full, basename($full));
                $processed = $imageService->coverWideWebp($uploaded, 1280, 'literature_workshops/covers');
                $record->cover_image_path = $processed['path'];
                $dirty = true;
            }
        }
        if ($record->presenter_avatar_path && str_contains($record->presenter_avatar_path, '/raw/')) {
            $full = storage_path('app/public/' . $record->presenter_avatar_path);
            if (is_file($full)) {
                $uploaded = new \Illuminate\Http\UploadedFile($full, basename($full));
                $processed = $imageService->uploadAndCrop($uploaded, 'literature_workshops/presenters', 400, 400);
                $record->presenter_avatar_path = $processed['path'];
                $dirty = true;
            }
        }
        if ($dirty)
            $record->save();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withCount('registrations');
    }
}
