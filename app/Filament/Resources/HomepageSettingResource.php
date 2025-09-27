<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageSettingResource\Pages;
use App\Models\HomepageSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageSettingResource extends Resource
{
    protected static ?string $model = HomepageSetting::class;
    protected static ?string $slug = 'homepage-settings';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'الصفحة الرئيسية';
    protected static ?string $pluralLabel = 'إعدادات الصفحة الرئيسية';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('إعدادات الصفحة')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('الهيرو')->id('hero')->schema([
                        Forms\Components\Textarea::make('hero_text')->label('نص الهيرو')->rows(3),
                        Forms\Components\FileUpload::make('hero_bg_image')->label('صورة الخلفية')
                            ->disk('public')->directory('settings')->image()->visibility('public'),
                        Forms\Components\FileUpload::make('hero_logo')->label('شعار')
                            ->disk('public')->directory('settings')->image()->visibility('public'),
                    ])->columns(2),
                    Forms\Components\Tabs\Tab::make('الفنان المميز')->id('featured')->schema([
                        Forms\Components\TextInput::make('featured_artist_title')->label('العنوان'),
                        Forms\Components\Textarea::make('featured_artist_description')->label('الوصف')->rows(3),
                        Forms\Components\FileUpload::make('featured_artist_image')->label('الصورة')
                            ->disk('public')->directory('settings')->image()->visibility('public'),
                    ])->columns(2),
                    Forms\Components\Tabs\Tab::make('شرائح الفن')->id('slides')->schema([
                        Forms\Components\Repeater::make('art_slides')->label('الشرائح')->schema([
                            Forms\Components\TextInput::make('title')->label('العنوان')->required(),
                            Forms\Components\Textarea::make('description')->label('الوصف')->rows(2)->required(),
                            Forms\Components\FileUpload::make('image')->label('الصورة')
                                ->disk('public')->directory('settings/slides')->image()->visibility('public')->required(),
                        ])->columns(2)->addActionLabel('إضافة شريحة')->collapsible()->reorderable(),
                    ]),
                    Forms\Components\Tabs\Tab::make('المزادات')->id('auctions')->schema([
                        Forms\Components\TextInput::make('auctions_title')->label('عنوان قسم المزادات'),
                        Forms\Components\Textarea::make('auctions_subtitle')->label('وصف قسم المزادات')->rows(2),
                    ]),
                    Forms\Components\Tabs\Tab::make('الفعاليات')->id('events')->schema([
                        Forms\Components\Repeater::make('events')->label('قائمة الفعاليات')->schema([
                            Forms\Components\TextInput::make('title')->label('العنوان')->required(),
                            Forms\Components\Textarea::make('description')->label('الوصف')->rows(2)->required(),
                            Forms\Components\TextInput::make('day')->label('اليوم')->numeric()->minValue(1)->maxValue(31)->required(),
                            Forms\Components\TextInput::make('month')->label('الشهر')->required(),
                            Forms\Components\TextInput::make('link')->label('رابط (اختياري)'),
                        ])->columns(2)->addActionLabel('إضافة فعالية')->collapsible()->reorderable(),
                    ]),
                ])->columnSpanFull(),
            Forms\Components\TextInput::make('upload_max_mb')
                ->label('حد رفع ملفات التوثيق (MB)')
                ->numeric()
                ->default(40)
                ->minValue(1)
                ->helperText('الحد الأقصى لحجم الملفات المرفوعة في طلب التوثيق. يجب أن يكون أقل من post_max_size في php.ini.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('updated_at')->label('آخر تحديث')->dateTime()->sortable(),
        ])->actions([
                    Tables\Actions\EditAction::make(),
                ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomepageSettings::route('/'),
            'create' => Pages\CreateHomepageSetting::route('/create'),
            'edit' => Pages\EditHomepageSetting::route('/{record}/edit'),
            'edit-hero' => Pages\EditHomepageHero::route('/{record}/edit/hero'),
            'edit-featured' => Pages\EditHomepageFeatured::route('/{record}/edit/featured'),
            'edit-slides' => Pages\EditHomepageSlides::route('/{record}/edit/slides'),
            'edit-auctions' => Pages\EditHomepageAuctions::route('/{record}/edit/auctions'),
            'edit-events' => Pages\EditHomepageEvents::route('/{record}/edit/events'),
        ];
    }
}

