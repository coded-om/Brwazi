<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiteratureWorkshopRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'literature_workshop_id',
        'user_id',
        'name',
        'email',
        'phone',
        'whatsapp_phone',
        'notes',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(LiteratureWorkshop::class, 'literature_workshop_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
