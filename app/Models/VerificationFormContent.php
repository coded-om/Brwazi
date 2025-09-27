<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationFormContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_type',
        'terms',
        'attachments',
        'works_min',
        'works_max',
    ];

    protected $casts = [
        'terms' => 'array',
        'attachments' => 'array',
    ];

    public static function for(string $formType): self
    {
        return static::firstOrCreate(
            ['form_type' => $formType],
            [
                'terms' => [],
                'attachments' => [],
                'works_min' => $formType === VerificationRequest::FORM_PHOTO ? 10 : 5,
                'works_max' => 10,
            ]
        );
    }
}
