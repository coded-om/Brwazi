<div align="center">

![Brwazi Logo](public/imgs/logo.svg)

# Brwazi Platform

منصّة فنية / أدبية / معارض تفاعلية – إدارة، عرض، مزادات، وورش عمل.

**English summary below.**

</div>

## 📌 المحتوى

1. [وصف المشروع](#-وصف-المشروع)
2. [المزايا الرئيسية](#-المزايا-الرئيسية)
3. [المخطط العام للتقنيات](#-التقنيات)
4. [التجهيز السريع (Quick Start)](#-التجهيز-السريع)
5. [إعداد ملف البيئة .env](#-إعداد-البيئة-env)
6. [بناء الواجهة الأمامية](#-الواجهة-الأمامية)
7. [إدارة الصور وملف .gitignore](#-الصور)
8. [المعارض (Exhibitions) وواجهة JSON](#-المعارض-exhibitions)
9. [هيكل المجلدات المختصر](#-هيكل-المجلدات)
10. [الاختبارات](#-الاختبارات)
11. [الأمان](#-الأمان)
12. [المساهمة](#-المساهمة)
13. [الترخيص](#-الترخيص)
14. [English Summary](#-english-summary)

## 🖼 وصف المشروع

Brwazi منصة متعددة الأقسام لإدارة وعرض:

-   الأعمال الفنية (Artworks)
-   الأعمال الأدبية (Literary)
-   ورش فنية + ورش أدبية
-   معرض برواز (Art Brwaz)
-   المعارض الواقعية مع خريطة تفاعلية (Leaflet)
-   المزاد الفني (مزاد مباشر/مستقبلي)
-   لوحة مستخدم (Dashboard) وحساب شخصي
-   إدارة كاملة عبر Filament Admin (CRUD / Forms / Media)

تم التركيز على تجربة مستخدم متجاوبة (Desktop / Tablet / Mobile) مع قوائم ديناميكية، تحميل تدريجي، وخريطة تفاعلية.

## 🚀 المزايا الرئيسية

-   إدارة المعارض مع تحديد الإحداثيات (خرائط Leaflet + اختيار النقطة).
-   واجهة عرض بطريقتين (Grid / Map) مع حفظ التفضيل في LocalStorage.
-   تحميل المزيد (Load More) + استجابة JSON مع meta (صفحات، إجمالي، ...).
-   معرض (Art Brwaz) مستقل.
-   ورش فنية وأدبية (منسوخة الهيكلة لتوحيد التجربة).
-   خاصية المزاد الفني.
-   تحسين مسارات الصور عبر `asset_url` (التعامل مع التخزين / التخزين الرمزي).
-   شريط تنقل يكيف الحالة النشطة ديناميكيًا.
-   Filament Resource لكل كيان أساسي (Exhibition, Workshop, Artwork, Book, ...).
-   دعم دفع Thawani (قابل للتفعيل عبر البيئة).
-   Tailwind + Vite + PostCSS.
-   تحسين تجربة التحميل (Skeletons + Empty State).

## 🛠 التقنيات

| الجزء      | التقنية                                        |
| ---------- | ---------------------------------------------- |
| الإطار     | Laravel 10+                                    |
| الواجهة    | Blade + TailwindCSS + Alpine.js (خفيف)         |
| الإدارة    | FilamentPHP                                    |
| الخرائط    | Leaflet 1.9.x                                  |
| البناء     | Vite                                           |
| الصور      | Storage + symlink (`php artisan storage:link`) |
| المزاد     | نماذج + جداول مخصصة                            |
| الدفع      | Thawani (اختياري)                              |
| الاختبارات | PHPUnit                                        |

## ⚡ التجهيز السريع

```bash
git clone <repo-url> brwazi
cd brwazi
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # (اختياري حسب توفر seeders)
php artisan storage:link
npm install
npm run dev                  # أو npm run build للإنتاج
php artisan serve            # http://127.0.0.1:8000
```

على Windows يفضَّل تشغيل الأوامر في Git Bash أو PowerShell (باستثناء ما يتطلب CMD فقط).

## 🔧 إعداد البيئة (.env)

أهم المتغيرات:

```env
APP_NAME=Brwazi
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=brwazi
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

QUEUE_CONNECTION=database
CACHE_STORE=file
SESSION_DRIVER=file

# Thawani (اختياري)
THAWANI_SECRET=sk_test_xxx
THAWANI_BASE_URL=https://checkout.thawani.om

# Mail (مثال Mailgun)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@example
MAIL_PASSWORD=xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Brwazi"
```

## 🎨 الواجهة الأمامية

أوامر مفيدة:

```bash
npm run dev      # تطوير + HMR
npm run build    # إنتاج مع Minify
npm run preview  # معاينة إنتاجية (إن وُجد السكربت)
```

التصميم يعتمد على Tailwind. تجنّب استخدام `@apply` داخل شفرات Inline (تم إزالته في بعض الأقسام واستبداله بـ Classes).

## 🖼 الصور

-   تم تعديل `.gitignore` للسماح برفع `public/imgs/uploads` (أزلنا السطر الذي كان يستثنيه).
-   اجعل عمليات الرفع تذهب إلى `storage/app/public` ثم استخدم `storage:link` للوصول عبر `/storage/...` عند الحاجة.
-   إن رغبت بمنع رفع ملفات ضخمة مستقبلاً استخدم Git LFS أو أعد تفعيل الاستثناء.

## 🗺 المعارض (Exhibitions)

نقطة نهاية JSON (مثال):

```
GET /exhibitions?page=2&per_page=12
```

الاستجابة:

```json
{
	"data": [ { "id": 10, "title": "...", "lat": 23.5, "lng": 58.38, ... } ],
	"meta": {
		"current_page": 2,
		"last_page": 5,
		"per_page": 12,
		"total": 57,
		"has_more": true
	}
}
```

الواجهة الأمامية تستخدم "Load More" لإلحاق النتائج + إضافة Marker للخريطة بدون إعادة تحميل الصفحة.

## 📂 هيكل المجلدات (مختصر)

```
app/
	Models/             # النماذج (Artwork, Exhibition, Workshop ...)
	Http/Controllers/   # التحكم + API/Views
	Filament/           # الموارد الإدارية
resources/views/      # Blade templates
public/               # الأصول النهائية (imgs, build, ...)
routes/web.php        # المسارات العامة
routes/api.php        # مسارات API الإضافية إن وجدت
database/migrations/  # الترحيلات
```

## 🧪 الاختبارات

تشغيل:

```bash
php artisan test
# أو
./vendor/bin/phpunit
```

أضف اختبارات للوحدات (Unit) + سلوك (Feature) خصوصًا للمعارض والمزادات.

## 🔐 الأمان

-   لا ترفع ملف `.env` أبدًا.
-   راجع صلاحيات رفع الملفات (الامتدادات / الحجم).
-   استخدم CSRF و Validation (موجود افتراضيًا في Laravel).
-   نظّم صلاحيات لوحة التحكم (Filament Policies إن لزم).

## 🤝 المساهمة

Pull Requests مرحب بها. اقترح أولاً في Issue عند التغيير الجذري.  
معايير مقترحة:

1. التزم PSR-12.
2. استخدم أسماء دلالية للفروع: `feature/exhibitions-filter` أو `fix/image-path`.
3. اختبارات عند إضافة منطق عمل جديد.

## 📄 الترخيص

المشروع نفسه (ما لم يُذكر خلافه) تحت رخصة MIT. راجع رأس الملفات أو أضف LICENSE لاحقًا إن لم توجد.

---

## 🌍 English Summary

Brwazi is a multi-section Laravel platform: artworks, literary content, art & literature workshops, physical & virtual exhibitions (with Leaflet map + coordinate picker), auctions, and an Art Brwaz gallery. It ships with a responsive multi-breakpoint navigation, JSON pagination for exhibitions, Filament admin resources, Thawani payment integration (optional), Tailwind + Vite asset pipeline, and progressive UX patterns (skeleton loaders, load-more, local preference persistence).

### Key Features

-   Exhibitions with geo markers + map/grid toggle.
-   Workshops (art + literature) & gallery.
-   Auction module (extensible).
-   Filament admin CRUD for core entities.
-   Thawani integration (via ENV keys).
-   Asset pipeline (Vite, Tailwind, PostCSS).
-   Custom asset helper for resilient image paths.

### Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run dev
php artisan serve
```

### Env (Excerpt)

```
THAWANI_SECRET=sk_test_xxx
THAWANI_BASE_URL=https://checkout.thawani.om
FILESYSTEM_DISK=public
```

### Exhibitions JSON

```
GET /exhibitions?page=1&per_page=12
```

Returns `{ data: [...], meta: { current_page, last_page, total, has_more } }`.

### Contributing

Open an issue → branch → PR. Include tests where logic changes.

---

إذا احتجت توثيقًا إضافيًا (مثلاً: إعداد المزاد، أو بث حي)، أضف Issue أو حدث README لاحقًا.

شكراً لاستخدامك Brwazi 💜
