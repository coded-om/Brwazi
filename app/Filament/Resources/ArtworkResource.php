<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtworkResource\Pages;
use App\Models\Artwork;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArtworkResource extends Resource
{
    protected static ?string $model = Artwork::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'المحتوى';

    protected static ?string $modelLabel = 'عمل فني';

    protected static ?string $pluralModelLabel = 'الأعمال الفنية';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')->relationship('user', 'email')->required(),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
                Forms\Components\Select::make('category')->options(Artwork::categories())->required(),
                Forms\Components\TextInput::make('price')->numeric()->prefix('ر.ع')->nullable(),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'مسودة',
                    'published' => 'منشور',
                ])->required(),
                Forms\Components\Toggle::make('allow_offers')->label('السماح بالعروض'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('المالك')->searchable(),
                Tables\Columns\TextColumn::make('category')->label('الفئة'),
                Tables\Columns\TextColumn::make('price')->money('OMR')->label('السعر'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'success' => 'published',
                ])->label('الحالة'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('منذ'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListArtworks::route('/'),
            'create' => Pages\CreateArtwork::route('/create'),
            'edit' => Pages\EditArtwork::route('/{record}/edit'),
        ];
    }
}
