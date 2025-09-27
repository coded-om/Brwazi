<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaglineResource\Pages;
use App\Models\Tagline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaglineResource extends Resource
{
    protected static ?string $model = Tagline::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'المستخدمون';
    protected static ?string $modelLabel = 'تخصص';
    protected static ?string $pluralModelLabel = 'التخصصات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('الاسم')->required()->unique(ignoreRecord: true),
            Forms\Components\Toggle::make('active')->label('مفعل')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0)->label('الترتيب'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->label('الاسم'),
            Tables\Columns\IconColumn::make('active')->boolean()->label('مفعل'),
            Tables\Columns\TextColumn::make('sort_order')->sortable()->label('ترتيب'),
            Tables\Columns\TextColumn::make('created_at')->since()->label('منذ'),
        ])->defaultSort('sort_order')
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
            'index' => Pages\ListTaglines::route('/'),
            'create' => Pages\CreateTagline::route('/create'),
            'edit' => Pages\EditTagline::route('/{record}/edit'),
        ];
    }
}
