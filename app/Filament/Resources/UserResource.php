<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Tagline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'المستخدمون';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fname')->label('الاسم')->required(),
                Forms\Components\TextInput::make('lname')->label('اللقب')->required(),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'هذا البريد مستخدم بالفعل',
                    ]),
                Forms\Components\TextInput::make('phone_number'),
                Forms\Components\Select::make('tagline')
                    ->label('التخصص')
                    ->options(fn() => Tagline::query()->where('active', true)->orderBy('sort_order')->orderBy('id')->pluck('name', 'name')->toArray())
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('status')->options([
                    0 => 'عادي',
                    1 => 'موثق',
                    2 => 'مميز',
                    3 => 'محظور',
                ])->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('full_name')->label('الاسم')->getStateUsing(fn(User $r) => $r->full_name)->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('tagline')->label('التخصص')->toggleable(isToggledHiddenByDefault: true)->limit(30),
                Tables\Columns\BadgeColumn::make('status')->label('الحالة')
                    ->colors([
                        'gray' => 0,
                        'success' => 1,
                        'warning' => 2,
                        'danger' => 3,
                    ])
                    ->formatStateUsing(fn($state) => match ($state) { 0 => 'عادي', 1 => 'موثق', 2 => 'مميز', 3 => 'محظور', default => 'غير معروف'}),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
