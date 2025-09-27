<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationRequestResource\Pages;
use App\Models\VerificationRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class VerificationRequestResource extends Resource
{
    protected static ?string $model = VerificationRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'المستخدمون';

    protected static ?string $modelLabel = 'طلب توثيق';

    protected static ?string $pluralModelLabel = 'طلبات التوثيق';

    public static function getNavigationBadge(): ?string
    {
        return (string) VerificationRequest::query()->where('status', VerificationRequest::STATUS_PENDING)->count();
    }

    public static function form(Form $form): Form
    {
        // Admins don't create these; keep an empty/readonly form to satisfy Resource API
        return $form->schema([
            Forms\Components\Textarea::make('decision_notes')->label('ملاحظات القرار')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('user.email')->label('المستخدم')->searchable(),
                Tables\Columns\TextColumn::make('full_name')->label('الاسم')->searchable(),
                Tables\Columns\BadgeColumn::make('form_type')->label('نوع الاستمارة')
                    ->colors([
                        'info' => 'visual',
                        'warning' => 'photo',
                    ])->formatStateUsing(fn($state) => $state === 'photo' ? 'تصوير' : 'فنون بصرية'),
                Tables\Columns\BadgeColumn::make('status')->label('الحالة')
                    ->colors([
                        'warning' => VerificationRequest::STATUS_PENDING,
                        'success' => VerificationRequest::STATUS_APPROVED,
                        'danger' => VerificationRequest::STATUS_REJECTED,
                    ])->formatStateUsing(function ($state) {
                        return match ($state) {
                            VerificationRequest::STATUS_PENDING => 'قيد المراجعة',
                            VerificationRequest::STATUS_APPROVED => 'مقبول',
                            VerificationRequest::STATUS_REJECTED => 'مرفوض',
                            default => $state,
                        };
                    })->sortable(),
                Tables\Columns\TextColumn::make('submitted_at')->label('تاريخ التقديم')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')->label('تاريخ المراجعة')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('الحالة')->options([
                    VerificationRequest::STATUS_PENDING => 'قيد المراجعة',
                    VerificationRequest::STATUS_APPROVED => 'مقبول',
                    VerificationRequest::STATUS_REJECTED => 'مرفوض',
                ]),
                Tables\Filters\SelectFilter::make('form_type')->label('نوع الاستمارة')->options([
                    'visual' => 'فنون بصرية',
                    'photo' => 'تصوير',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === VerificationRequest::STATUS_PENDING)
                    ->form([
                        Forms\Components\Textarea::make('decision_notes')->label('ملاحظات (اختياري)')->maxLength(2000),
                    ])
                    ->action(function (array $data, VerificationRequest $record) {
                        $record->markApproved(auth('admin')->id(), $data['decision_notes'] ?? null);
                        Notification::make()->title('تمت الموافقة على الطلب')->success()->send();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === VerificationRequest::STATUS_PENDING)
                    ->form([
                        Forms\Components\Textarea::make('decision_notes')->label('سبب الرفض')->required()->maxLength(2000),
                    ])
                    ->action(function (array $data, VerificationRequest $record) {
                        $record->markRejected(auth('admin')->id(), $data['decision_notes'] ?? null);
                        Notification::make()->title('تم رفض الطلب')->danger()->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('بيانات مقدم الطلب')->schema([
                    Components\TextEntry::make('user.email')->label('المستخدم'),
                    Components\TextEntry::make('full_name')->label('الاسم الكامل'),
                    Components\TextEntry::make('birth_date')->label('تاريخ الميلاد')->date('Y-m-d'),
                    Components\TextEntry::make('gender')->label('الجنس'),
                    Components\TextEntry::make('education')->label('التعليم'),
                    Components\TextEntry::make('address')->label('العنوان')->columnSpanFull(),
                    Components\TextEntry::make('phone')->label('الهاتف'),
                    Components\TextEntry::make('email')->label('الإيميل'),
                    Components\TextEntry::make('nationality')->label('الجنسية'),
                    Components\TextEntry::make('specialties')->label('التخصصات')
                        ->formatStateUsing(function ($state) {
                            if (is_array($state))
                                return implode('، ', $state);
                            return (string) $state;
                        }),
                ])->columns(2),

                Components\Section::make('المرفقات')->schema([
                    Components\TextEntry::make('id_file_url')->label('هوية')
                        ->html()
                        ->formatStateUsing(function ($state) {
                            if (!$state)
                                return '-';
                            $url = $state;
                            $u = e($url);
                            return "<a href=\"$u\" target=\"_blank\"><img src=\"$u\" alt=\"ID\" class=\"max-h-64 w-auto rounded border shadow-sm\"></a>";
                        }),
                    Components\TextEntry::make('avatar_file_url')->label('صورة شخصية')
                        ->html()
                        ->formatStateUsing(function ($state) {
                            if (!$state)
                                return '-';
                            $url = $state;
                            $u = e($url);
                            return "<a href=\"$u\" target=\"_blank\"><img src=\"$u\" alt=\"Avatar\" class=\"max-h-64 w-auto rounded-full border shadow-sm\"></a>";
                        }),
                    Components\TextEntry::make('cv_file_url')->label('السيرة الذاتية')
                        ->formatStateUsing(fn($state) => $state ?: '-')
                        ->url(fn($state) => $state ?: null, shouldOpenInNewTab: true)
                        ->copyable(),
                    Components\TextEntry::make('works_file_urls')->label('الأعمال')
                        ->html()
                        ->formatStateUsing(function ($state) {
                            if (!is_array($state) || empty($state))
                                return '-';
                            $items = array_map(function ($url) {
                                $u = e($url);
                                return "<a href=\"$u\" target=\"_blank\"><img src=\"$u\" class=\"w-full h-40 object-cover rounded border\" loading=\"lazy\" /></a>";
                            }, $state);
                            return '<div class="grid grid-cols-2 md:grid-cols-4 gap-3">' . implode('', array_map(fn($i) => "<div>" . $i . "</div>", $items)) . '</div>';
                        })
                        ->columnSpanFull(),
                ])->columns(3),

                Components\Section::make('القرار')->schema([
                    Components\TextEntry::make('status')->label('الحالة')
                        ->badge()
                        ->colors([
                            'warning' => VerificationRequest::STATUS_PENDING,
                            'success' => VerificationRequest::STATUS_APPROVED,
                            'danger' => VerificationRequest::STATUS_REJECTED,
                        ])->formatStateUsing(function ($state) {
                            return match ($state) {
                                VerificationRequest::STATUS_PENDING => 'قيد المراجعة',
                                VerificationRequest::STATUS_APPROVED => 'مقبول',
                                VerificationRequest::STATUS_REJECTED => 'مرفوض',
                                default => $state,
                            };
                        }),
                    Components\TextEntry::make('decision_notes')->label('الملاحظات')->columnSpanFull(),
                    Components\TextEntry::make('admin_id')->label('المدقق')->formatStateUsing(fn($state) => $state ? '#' . $state : '-'),
                    Components\TextEntry::make('submitted_at')->label('تم التقديم')->dateTime('Y-m-d H:i'),
                    Components\TextEntry::make('reviewed_at')->label('تمت المراجعة')->dateTime('Y-m-d H:i'),
                ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerificationRequests::route('/'),
            'view' => Pages\ViewVerificationRequest::route('/{record}'),
        ];
    }
}
