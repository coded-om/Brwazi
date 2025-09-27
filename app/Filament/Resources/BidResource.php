<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BidResource\Pages;
use App\Models\Bid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BidResource extends Resource
{
    protected static ?string $model = Bid::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'المزادات';

    protected static ?string $modelLabel = 'عرض مزايدة';

    protected static ?string $pluralModelLabel = 'عروض المزايدة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('auction_id')->relationship('auction', 'id')->required(),
                Forms\Components\Select::make('user_id')->relationship('user', 'email')->searchable()->required(),
                Forms\Components\TextInput::make('amount')->numeric()->prefix('ر.ع')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('auction_id')->label('مزاد')->sortable(),
                Tables\Columns\TextColumn::make('user.email')->label('مستخدم')->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('OMR')->label('المبلغ')->sortable(),
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
            'index' => Pages\ListBids::route('/'),
            'create' => Pages\CreateBid::route('/create'),
            'edit' => Pages\EditBid::route('/{record}/edit'),
        ];
    }
}
