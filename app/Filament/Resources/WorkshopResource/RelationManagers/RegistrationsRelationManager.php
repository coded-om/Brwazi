<?php

namespace App\Filament\Resources\WorkshopResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';
    protected static ?string $title = 'المسجلون';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('البريد')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('الجوال'),
                Tables\Columns\TextColumn::make('whatsapp_phone')->label('واتساب')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->since(),
            ])
            ->filters([
                Filter::make('registered_range')
                    ->label('تاريخ التسجيل')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('من'),
                        Forms\Components\DatePicker::make('until')->label('إلى'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('تفاصيل التسجيل')
                    ->modalContent(fn($record) => view('admin.partials.workshop-registration-details', ['record' => $record]))
            ])
            ->bulkActions([]);
    }
}
