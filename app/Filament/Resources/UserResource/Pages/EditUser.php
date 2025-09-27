<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Trim email just in case
        if (isset($data['email'])) {
            $data['email'] = trim($data['email']);
        }
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                Notification::make()
                    ->title('هذا البريد مستخدم بالفعل')
                    ->danger()
                    ->send();
                // Re-throwing is not needed; just keep old data
                $this->halt();
            }
            throw $e;
        }
        return $record;
    }
}
