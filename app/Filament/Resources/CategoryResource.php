<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'القسم الأدبي';

    protected static ?string $modelLabel = 'تصنيف';
    protected static ?string $pluralModelLabel = 'التصنيفات';

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
                Forms\Components\Select::make('parent_id')
                    ->label('التصنيف الأب')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('بدون أب (تصنيف رئيسي)')
                    ->helperText('اجعل الحقل فارغًا ليكون التصنيف رئيسيًا، أو اختر تصنيفًا ليصبح هذا التصنيف فرعيًا منه.')
                    ->nullable(),
                Forms\Components\Select::make('type')->label('النوع')->options([
                    'genre' => 'نوع أدبي',
                    'topic' => 'موضوع',
                ])->default('genre')->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parent.name')->label('التصنيف الأب (الرئيسي)')->toggleable(),
                Tables\Columns\TextColumn::make('type')->label('النوع')->badge()->toggleable(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
