<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionRequestResource\Pages;
use App\Models\AuctionRequest;
use App\Models\Auction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AuctionRequestResource extends Resource
{
    protected static ?string $model = AuctionRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'المزادات';

    protected static ?string $modelLabel = 'طلب مزاد';

    protected static ?string $pluralModelLabel = 'طلبات المزاد';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('artwork_id')->relationship('artwork', 'title')->required(),
                Forms\Components\Select::make('user_id')->relationship('user', 'email')->searchable()->required(),
                Forms\Components\TextInput::make('desired_start_price')->numeric()->prefix('ر.ع')->required(),
                Forms\Components\DateTimePicker::make('suggested_start_at')->seconds(false),
                Forms\Components\TextInput::make('suggested_duration')->numeric()->suffix('دقيقة'),
                Forms\Components\Textarea::make('admin_notes')->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('artwork.title')->label('العمل')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('المستخدم')->searchable(),
                Tables\Columns\BadgeColumn::make('status')->label('الحالة')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])->sortable(),
                Tables\Columns\TextColumn::make('desired_start_price')->money('OMR')->label('سعر البداية'),
                Tables\Columns\TextColumn::make('suggested_start_at')->dateTime('Y-m-d H:i'),
                Tables\Columns\TextColumn::make('suggested_duration')->suffix(' دقيقة'),
                Tables\Columns\TextColumn::make('reviewed_at')->dateTime('Y-m-d H:i'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'قيد المراجعة',
                    'approved' => 'مقبول',
                    'rejected' => 'مرفوض',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('موافقة + جدولة')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\TextInput::make('start_price')->numeric()->required()->prefix('ر.ع'),
                        Forms\Components\TextInput::make('bid_increment')->numeric()->required()->prefix('ر.ع'),
                        Forms\Components\DateTimePicker::make('starts_at')->required()->seconds(false),
                        Forms\Components\TextInput::make('duration_minutes')->numeric()->minValue(5)->maxValue(10080)->required()->suffix(' دقيقة'),
                        Forms\Components\TextInput::make('reserve_price')->numeric()->prefix('ر.ع')->nullable(),
                        Forms\Components\TextInput::make('buy_now_price')->numeric()->prefix('ر.ع')->nullable(),
                    ])
                    ->action(function (array $data, AuctionRequest $record) {
                        $startsAt = \Carbon\Carbon::parse($data['starts_at']);
                        $endsAt = (clone $startsAt)->addMinutes((int) $data['duration_minutes']);

                        $existsActive = Auction::where('artwork_id', $record->artwork_id)
                            ->whereIn('status', ['draft', 'scheduled', 'live'])
                            ->exists();
                        if ($existsActive) {
                            Notification::make()
                                ->title('هناك مزاد نشط/مجدول مسبقاً لهذه اللوحة.')
                                ->warning()
                                ->send();
                            return;
                        }

                        Auction::create([
                            'artwork_id' => $record->artwork_id,
                            'status' => 'scheduled',
                            'starts_at' => $startsAt,
                            'ends_at' => $endsAt,
                            'start_price' => (float) $data['start_price'],
                            'bid_increment' => (float) $data['bid_increment'],
                            'reserve_price' => $data['reserve_price'] ?? null,
                            'buy_now_price' => $data['buy_now_price'] ?? null,
                            'approved_by_admin_id' => auth('admin')->id(),
                        ]);

                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth('admin')->id(),
                            'reviewed_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')->label('ملاحظات')->maxLength(2000),
                    ])
                    ->action(function (array $data, AuctionRequest $record) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'] ?? null,
                            'reviewed_by' => auth('admin')->id(),
                            'reviewed_at' => now(),
                        ]);
                    }),
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
            'index' => Pages\ListAuctionRequests::route('/'),
            'create' => Pages\CreateAuctionRequest::route('/create'),
            'edit' => Pages\EditAuctionRequest::route('/{record}/edit'),
        ];
    }
}
