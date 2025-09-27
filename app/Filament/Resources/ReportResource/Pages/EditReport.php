<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReportStatusChanged;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function afterSave(): void
    {
        // إرسال إشعار تحديث حالة للمبلّغ عند التغيير
        $report = $this->record;
        if ($report->reporter) {
            try {
                $report->reporter->notify(new ReportStatusChanged($report));
            } catch (\Throwable $e) { /* ignore */
            }
        }
    }
}
