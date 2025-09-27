<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery3DSetting extends Model
{
    use HasFactory;

    protected $table = 'gallery3d_settings';

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'exhibit_url',
        'autoplay',
        'interval_ms',
        'slides',
        'active',
    ];

    protected $casts = [
        'autoplay' => 'boolean',
        'active' => 'boolean',
        'interval_ms' => 'integer',
        'slides' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'hero_title' => '🖼️ معارض برواز',
            'hero_subtitle' => 'اكتشف عوالم ثلاثية الأبعاد تفاعلية',
            'exhibit_url' => 'https://www.artsteps.com/embed/68c5668548bbdfa0b611ff85/560/315',
            'autoplay' => false,
            'interval_ms' => 6000,
            'slides' => [
                [
                    'title' => 'معرض المرأة العمانية',
                    'description' => 'في عام 1938، وضع طه حسين كتابه الشهير "مستقبل الثقافة في مصر"، موضحاً أن قرار التأليف جاء لوضع الخطوط العامة لثقافة مصر في مرحلة حرجة.',
                    'model_path' => 'imgs/artBrwaz/1a61b574-7f79-4c83-b023-14317f94b715_textured_mesh.glb',
                    'cta_text' => 'دخول',
                    'cta_link' => null,
                ],
                [
                    'title' => 'معرض الإبداع الفني',
                    'description' => 'تجربة فن رقمي ثلاثي الأبعاد تمزج الإبداع بالتقنية الحديثة وتمنح المشاهد رحلة بصرية غامرة.',
                    'model_path' => 'imgs/artBrwaz/house_for_game.glb',
                    'cta_text' => 'دخول',
                    'cta_link' => null,
                ],
                [
                    'title' => 'معرض التراث والهوية',
                    'description' => 'رحلة عبر الزمن لاستكشاف عناصر التراث والهوية العربية بأسلوب يجمع بين الأصالة والتجديد.',
                    'model_path' => 'imgs/artBrwaz/bottle_grass_plant.glb',
                    'cta_text' => 'دخول',
                    'cta_link' => null,
                ],
            ],
            'active' => true,
        ]);
    }
}
