<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Gallery3DSettingResource\Pages;
use App\Models\Gallery3DSetting;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class Gallery3DSettingResource extends Resource
{
    protected static ?string $model = Gallery3DSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'معرض برواز 3D';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('العناوين')
                ->schema([
                    TextInput::make('hero_title')->label('العنوان')->maxLength(120),
                    TextInput::make('hero_subtitle')->label('وصف مختصر')->maxLength(200),
                ])->columns(2),
            Section::make('خيارات العرض')
                ->schema([
                    TextInput::make('exhibit_url')->label('رابط العرض (Artsteps)')->url(),
                    Grid::make(2)->schema([
                        Toggle::make('autoplay')->label('تشغيل تلقائي'),
                        TextInput::make('interval_ms')->numeric()->label('الفاصل الزمني (ms)')->default(6000),
                    ]),
                    Toggle::make('active')->label('مفعل')->default(true),
                ]),
            Section::make('الشرائح')
                ->schema([
                    Repeater::make('slides')->label('القائمة')
                        ->schema([
                            TextInput::make('title')->label('العنوان')->required(),
                            Textarea::make('description')->label('الوصف')->rows(3),
                            FileUpload::make('model_path')->label('ملف 3D (.glb)')
                                ->disk('public')
                                ->directory('settings/artbrwaz')
                                ->acceptedFileTypes(['model/gltf-binary', '.glb'])
                                ->downloadable()
                                ->preserveFilenames(),
                            Grid::make(2)->schema([
                                TextInput::make('cta_text')->label('نص الزر')->default('دخول'),
                                TextInput::make('cta_link')->label('رابط مخصص')->url()->nullable(),
                            ]),
                        ])->collapsible()->itemLabel(fn($state) => $state['title'] ?? 'شريحة')
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hero_title')->label('العنوان'),
                Tables\Columns\IconColumn::make('active')->boolean()->label('نشط'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGallery3DSettings::route('/'),
        ];
    }
}
