<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>تم إرسال الطلب</title>
    <link rel="icon" href="{{ asset('logo-color.ico') }}" />
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Naskh Arabic", "Noto Sans Arabic", "Helvetica Neue", Arial, "Tahoma", sans-serif;
            padding: 40px;
            background: #fbfbfe;
            color: #141640
        }

        .card {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid rgba(20, 22, 64, .06);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(20, 22, 64, .04)
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>تم إرسال طلب التوثيق</h1>
        <p>شكرًا لك. سيجري فريقنا مراجعة طلبك والرد خلال المدة المحددة.</p>
        <p><a href="{{ route('user.dashboard') }}">العودة إلى لوحة المستخدم</a></p>
    </div>
</body>

</html>