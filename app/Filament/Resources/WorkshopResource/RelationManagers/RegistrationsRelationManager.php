<?php

namespace App\Filament\Resources\WorkshopResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

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
