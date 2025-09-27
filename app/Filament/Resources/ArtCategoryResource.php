<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtCategoryResource\Pages;
use App\Models\ArtCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArtCategoryResource extends Resource
{
    protected static ?string $model = ArtCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $modelLabel = 'نوع العمل الفني';
    protected static ?string $pluralModelLabel = 'أنواع الأعمال الفنية';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('الاسم')->required()->maxLength(100),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0)->label('الترتيب'),
            Forms\Components\Toggle::make('active')->label('نشط')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
            Tables\Columns\TextColumn::make('slug')->label('المعرف')->copyable(),
            Tables\Columns\TextColumn::make('sort_order')->label('الترتيب')->sortable(),
            Tables\Columns\IconColumn::make('active')->boolean()->label('نشط'),
        ])->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtCategories::route('/'),
            'create' => Pages\CreateArtCategory::route('/create'),
            'edit' => Pages\EditArtCategory::route('/{record}/edit'),
        ];
    }
}
