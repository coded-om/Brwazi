<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'القسم الأدبي';

    protected static ?string $modelLabel = 'مؤلف';
    protected static ?string $pluralModelLabel = 'المؤلفون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('الاسم')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        $set('slug', Str::slug((string) $state));
                    }),
                Forms\Components\Hidden::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('bio')->label('نبذة')->rows(4),
                Forms\Components\FileUpload::make('avatar_path')->label('الصورة')
                    ->image()->directory('authors/avatars')->disk('public')->maxSize(8192),
                Forms\Components\Section::make('معلومات داخلية (للإدارة)')->schema([
                    Forms\Components\TextInput::make('phone')->label('الهاتف')->tel()->nullable(),
                    Forms\Components\TextInput::make('email')->label('البريد')->email()->nullable(),
                    Forms\Components\Textarea::make('notes')->label('ملاحظات')->rows(3)->nullable(),
                ])->collapsible()->collapsed(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->label('صورة')
                    ->getStateUsing(fn($record) => $record->avatar_path
                        ? asset('storage/' . ltrim($record->avatar_path, '/'))
                        : asset('imgs/pepole/artist-1.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
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
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
