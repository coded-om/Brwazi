<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionResource\Pages;
use App\Models\Auction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuctionResource extends Resource
{
    protected static ?string $model = Auction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'المزادات';

    protected static ?string $modelLabel = 'مزاد';

    protected static ?string $pluralModelLabel = 'المزادات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('artwork_id')
                            ->relationship('artwork', 'title')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('status')
                            ->label('الحالة')
                            ->required()
                            ->default('scheduled')
                            ->datalist(['draft', 'scheduled', 'live', 'ended', 'cancelled'])
                            ->maxLength(20),
                        Forms\Components\DateTimePicker::make('starts_at')->label('يبدأ في')->seconds(false),
                        Forms\Components\DateTimePicker::make('ends_at')->label('ينتهي في')->seconds(false),
                    ])->columns(2),

                Forms\Components\Section::make('الأسعار')
                    ->schema([
                        Forms\Components\TextInput::make('start_price')->numeric()->prefix('ر.ع')->required(),
                        Forms\Components\TextInput::make('bid_increment')->numeric()->prefix('ر.ع')->required(),
                        Forms\Components\TextInput::make('reserve_price')->numeric()->prefix('ر.ع')->nullable(),
                        Forms\Components\TextInput::make('buy_now_price')->numeric()->prefix('ر.ع')->nullable(),
                    ])->columns(2),

                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('artwork.title')->label('العمل')->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'live',
                        'danger' => 'ended',
                    ])->label('الحالة')->sortable(),
                Tables\Columns\TextColumn::make('start_price')->money('OMR')->label('سعر البدء')->sortable(),
                Tables\Columns\TextColumn::make('highest_bid_amount')->money('OMR')->label('أعلى عرض')->sortable(),
                Tables\Columns\TextColumn::make('bids_count')->label('العروض')->sortable(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->label('يبدأ')->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->label('ينتهي')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'مسودة',
                    'scheduled' => 'مجدول',
                    'live' => 'مباشر',
                    'ended' => 'منتهي',
                    'cancelled' => 'ملغي',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('startNow')
                    ->label('ابدأ الآن')
                    ->visible(fn($record) => $record->status === 'scheduled')
                    ->requiresConfirmation()
                    ->action(function (Auction $record) {
                        $record->update(['status' => 'live', 'starts_at' => now()]);
                    }),
                Tables\Actions\Action::make('endNow')
                    ->label('إنهاء الآن')
                    ->visible(fn($record) => in_array($record->status, ['live', 'scheduled']))
                    ->requiresConfirmation()
                    ->action(function (Auction $record) {
                        $record->update(['status' => 'ended', 'ends_at' => now()]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuctions::route('/'),
            'create' => Pages\CreateAuction::route('/create'),
            'edit' => Pages\EditAuction::route('/{record}/edit'),
        ];
    }
}
