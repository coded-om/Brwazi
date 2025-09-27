<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationFormContentResource\Pages;
use App\Models\VerificationFormContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VerificationFormContentResource extends Resource
{
    protected static ?string $model = VerificationFormContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?string $modelLabel = 'محتوى استمارة التوثيق';
    protected static ?string $pluralModelLabel = 'محتوى استمارات التوثيق';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('form_type')
                    ->label('نوع الاستمارة')
                    ->placeholder('مثال: visual أو photo أو نوع جديد')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Fieldset::make('الشروط')
                    ->schema([
                        Forms\Components\TagsInput::make('terms')
                            ->label('قائمة الشروط (اضف كل شرط كسطر مستقل)')
                            ->placeholder('اكتب شرطاً واضغط Enter')
                            ->reorderable()
                            ->separator(','),
                    ])->columns(1),

                Forms\Components\Fieldset::make('المرفقات المطلوبة')
                    ->schema([
                        Forms\Components\TagsInput::make('attachments')
                            ->label('قائمة المرفقات (اضف كل مرفق كسطر مستقل)')
                            ->placeholder('اكتب مرفقاً واضغط Enter')
                            ->reorderable()
                            ->separator(','),
                    ])->columns(1),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('works_min')
                            ->label('أقل عدد للأعمال')
                            ->numeric()
                            ->default(5)
                            ->required(),
                        Forms\Components\TextInput::make('works_max')
                            ->label('أقصى عدد للأعمال')
                            ->numeric()
                            ->default(10)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form_type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(function (string $state) {
                        return $state === 'photo' ? 'التصوير الضوئي' : 'الفنون التشكيلية والرقمية';
                    }),
                Tables\Columns\TextColumn::make('terms')
                    ->label('عدد الشروط')
                    ->state(fn($record) => is_array($record->terms) ? count($record->terms) : 0),
                Tables\Columns\TextColumn::make('attachments')
                    ->label('عدد المرفقات')
                    ->state(fn($record) => is_array($record->attachments) ? count($record->attachments) : 0),
                Tables\Columns\TextColumn::make('works_min')->label('الحد الأدنى'),
                Tables\Columns\TextColumn::make('works_max')->label('الحد الأقصى'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListVerificationFormContents::route('/'),
            'create' => Pages\CreateVerificationFormContent::route('/create'),
            'edit' => Pages\EditVerificationFormContent::route('/{record}/edit'),
        ];
    }
}
