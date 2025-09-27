<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'البلاغات';
    protected static ?string $pluralModelLabel = 'البلاغات';
    protected static ?string $modelLabel = 'بلاغ';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('البيانات')
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('النوع')
                        ->options(Report::types())
                        ->required(),
                    Forms\Components\Textarea::make('details')->label('التفاصيل')->rows(4),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options(Report::statuses())
                        ->required(),
                    Forms\Components\Textarea::make('notes')->label('ملاحظات داخلية')->rows(3),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('type')->label('النوع')->formatStateUsing(fn($state) => Report::types()[$state] ?? $state)->searchable(),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()->colors([
                    'warning' => 'pending',
                    'info' => 'reviewing',
                    'success' => 'resolved',
                    'danger' => 'rejected',
                ])->sortable(),
                Tables\Columns\TextColumn::make('reporter.email')->label('المبلّغ')->toggleable(),
                Tables\Columns\TextColumn::make('target_type')->label('نوع الهدف')->limit(20),
                Tables\Columns\TextColumn::make('created_at')->label('أنشئ')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Report::statuses()),
                Tables\Filters\SelectFilter::make('type')
                    ->label('النوع')
                    ->options(Report::types()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')
                    ->label('حل')
                    ->color('success')
                    ->visible(fn(Report $r) => $r->status !== Report::STATUS_RESOLVED)
                    ->action(fn(Report $r) => $r->update(['status' => Report::STATUS_RESOLVED])),
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
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
