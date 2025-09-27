<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Support\Facades\Storage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'القسم الأدبي';

    protected static ?string $modelLabel = 'كتاب';

    protected static ?string $pluralModelLabel = 'الكتب';

    public static function form(Form $form): Form
    {
        $publisherOptions = Publisher::query()->pluck('name', 'id')->toArray();
        $defaultPublisher = Publisher::query()->where('name', 'وزارة الثقافة')->value('id') ?? array_key_first($publisherOptions);

        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')->schema([
                    // نثبت الناشر إلى وزارة الثقافة (الإدارة) ولا نظهره في النموذج
                    Forms\Components\Hidden::make('publisher_id')
                        ->default($defaultPublisher)
                        ->required(),
                    Forms\Components\TextInput::make('title')->label('العنوان')->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            $set('slug', Str::slug((string) $state));
                        }),
                    Forms\Components\Hidden::make('slug')->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('isbn')->label('ISBN')->unique(ignoreRecord: true)->nullable(),
                    Forms\Components\Textarea::make('description')->label('الوصف')->rows(5)->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('الخصائص')->schema([
                    Forms\Components\Select::make('language')->label('اللغة')->options([
                        'ar' => 'العربية',
                        'en' => 'الإنجليزية',
                        'fr' => 'الفرنسية',
                    ])->default('ar')->required(),
                    Forms\Components\Select::make('type')->label('النوع')->options([
                        'novel' => 'رواية',
                        'poetry' => 'شعر',
                        'children' => 'أطفال',
                        'history' => 'تاريخ',
                        'science' => 'علوم',
                        'religion' => 'ديني',
                        'education' => 'تعليمي',
                        'theatre' => 'مسرح',
                        'short_story' => 'قصة قصيرة',
                        'criticism' => 'نقد أدبي',
                    ])->nullable(),
                    Forms\Components\TextInput::make('publish_year')->label('سنة النشر')->numeric()->minValue(1900)->maxValue((int) date('Y')),
                    Forms\Components\TextInput::make('pages')->label('الصفحات')->numeric()->minValue(1)->maxValue(5000),
                    Forms\Components\TextInput::make('stock')->label('المخزون')->numeric()->minValue(0)->default(0),
                    Forms\Components\Select::make('status')->label('الحالة')->options([
                        'published' => 'منشور',
                        'draft' => 'مسودة',
                    ])->default('published'),
                ])->columns(3),

                Forms\Components\Section::make('التسعير')->schema([
                    Forms\Components\TextInput::make('price_omr')->label('السعر (ر.ع)')->numeric()->required()->prefix('OMR')->rule('decimal:0,3'),
                    Forms\Components\TextInput::make('compare_at_price_omr')->label('السعر قبل الخصم')->numeric()->nullable()->prefix('OMR')->rule('decimal:0,3'),
                ])->columns(2),

                Forms\Components\Section::make('الوسائط')->schema([
                    Forms\Components\FileUpload::make('cover_image_path')->label('غلاف الكتاب')
                        ->image()->directory('books/covers')->disk('public')
                        ->imageEditor()->imageEditorAspectRatios(['2:3', '3:4', '1:1'])
                        ->maxSize(10240),
                    Forms\Components\Repeater::make('images')->label('صور إضافية')
                        ->relationship('images')
                        ->schema([
                            Forms\Components\FileUpload::make('path')->label('الصورة')
                                ->image()->directory('books/images')->disk('public')->maxSize(10240)
                                ->required(),
                            Forms\Components\TextInput::make('sort_order')->label('ترتيب')->numeric()->default(0),
                        ])->orderable('sort_order')->collapsible()->minItems(0)->maxItems(10)->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('العلاقات')->schema([
                    Forms\Components\Select::make('authors')->label('المؤلفون')
                        ->relationship('authors', 'name')
                        ->multiple()->preload()->searchable(),
                    Forms\Components\Select::make('categories')->label('التصنيفات')
                        ->relationship('categories', 'name')
                        ->multiple()->preload()->searchable(),
                ])->columns(2)->columnSpanFull(),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_path')
                    ->label('الغلاف')
                    ->getStateUsing(fn($record) => $record->cover_image_path
                        ? asset('storage/' . ltrim($record->cover_image_path, '/'))
                        : asset('imgs/pic/Book.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('title')->label('العنوان')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('publisher.name')->label('الناشر')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('language')->label('اللغة')->badge()->colors([
                    'primary' => 'ar',
                    'success' => 'en',
                    'warning' => 'fr',
                ]),
                Tables\Columns\TextColumn::make('type')->label('النوع')->toggleable(),
                Tables\Columns\TextColumn::make('publish_year')->label('السنة')->sortable(),
                Tables\Columns\TextColumn::make('price_omr')->label('السعر')->money('OMR')->sortable(),
                Tables\Columns\ToggleColumn::make('status')->label('منشور')
                    ->getStateUsing(fn($record) => $record->status === 'published')
                    ->afterStateUpdated(function ($record, $state) {
                        $record->update(['status' => $state ? 'published' : 'draft']);
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('publisher_id')->label('الناشر')->relationship('publisher', 'name'),
                Tables\Filters\SelectFilter::make('language')->label('اللغة')->options(['ar' => 'العربية', 'en' => 'الإنجليزية', 'fr' => 'الفرنسية']),
                Tables\Filters\SelectFilter::make('type')->label('النوع')->options([
                    'novel' => 'رواية',
                    'poetry' => 'شعر',
                    'children' => 'أطفال',
                    'history' => 'تاريخ',
                    'science' => 'علوم',
                    'religion' => 'ديني',
                    'education' => 'تعليمي',
                    'theatre' => 'مسرح',
                    'short_story' => 'قصة قصيرة',
                    'criticism' => 'نقد أدبي'
                ]),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
