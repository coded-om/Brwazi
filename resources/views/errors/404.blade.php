<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة غير موجودة - برواز</title>
    <meta name="robots" content="noindex">
    <style>
        :root {
            --brand: #181242;
            /* requested base color */
            --brand-2: #2a215e;
            /* complementary darker stop */
            --ink: #0F172A;
            --muted: #475569;
            --bg: #F8FAFC;
            --card: #FFFFFF;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Noto Sans, "Helvetica Neue", "Noto Naskh Arabic", Arial, "Apple Color Emoji", "Segoe UI Emoji";
            color: var(--ink);
            background: radial-gradient(1200px 600px at 90% -10%, rgba(24, 18, 66, 0.08), transparent),
                radial-gradient(800px 500px at 0% 110%, rgba(42, 33, 94, 0.08), transparent), var(--bg);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .wrap {
            width: 100%;
            max-width: 920px;
        }

        .card {
            background: var(--card);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .head {
            padding: 28px 28px 0 28px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(24, 18, 66, 0.12), rgba(42, 33, 94, 0.12));
            border: 1px solid rgba(24, 18, 66, 0.16);
        }

        .logo img {
            width: 26px;
            height: 26px;
            display: block;
        }

        .brand {
            font-weight: 800;
            letter-spacing: .3px;
            color: var(--brand);
        }

        .body {
            padding: 20px 28px 28px 28px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .illust {
            width: 100%;
            aspect-ratio: 3 / 1.4;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(24, 18, 66, 0.08), rgba(42, 33, 94, 0.08));
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(24px);
            opacity: 0.35;
        }

        .b1 {
            width: 220px;
            height: 220px;
            background: #181242;
            top: -40px;
            right: -40px;
        }

        .b2 {
            width: 260px;
            height: 260px;
            background: #2a215e;
            bottom: -60px;
            left: -30px;
        }

        h1 {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.15;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: clamp(14px, 2vw, 16px);
        }

        .cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: #fff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(24, 18, 66, 0.25);
            transform: translateZ(0);
        }

        .cta:active {
            transform: translateY(1px);
        }

        .row {
            display: grid;
            gap: 14px;
        }

        @media (min-width: 720px) {
            .body {
                grid-template-columns: 1.1fr 1fr;
                align-items: center;
            }
        }
    </style>
    <link rel="icon" href="{{ asset('logo-color.ico') }}">
    <meta name="theme-color" content="#181242">
    <meta name="color-scheme" content="light only">
    @php http_response_code(404); @endphp

</head>

<body>
    <main class="wrap" role="main" aria-label="الصفحة غير موجودة">
        <div class="card">
            <div class="head">
                <span class="logo" aria-hidden="true">
                    <img src="{{ asset('imgs/icons-color/logo-w-word.svg') }}" alt="شعار برواز">
                </span>
                <div>
                    <div class="brand">برواز</div>
                    <div style="font-size:12px;color:#64748B">Brwaz Platform</div>
                </div>
            </div>
            <div class="body">
                <div class="row">
                    <h3>عذرًا، الصفحة غير موجودة (404)</h4>
                        <p>يبدو أن الرابط غير صحيح أو أن الصفحة تم نقلها. يمكنك العودة إلى الصفحة الرئيسية.</p>
                        <div>
                            <a class="cta" href="{{ route('home') }}" aria-label="العودة إلى الصفحة الرئيسية">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 12L12 3L21 12" stroke="white" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M5 10V21H19V10" stroke="white" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span>العودة للصفحة الرئيسية</span>
                            </a>
                        </div>
                </div>
                <div class="illust" aria-hidden>
                    <div class="blob b1"></div>
                    <div class="blob b2"></div>
                    <svg width="220" height="120" viewBox="0 0 220 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="10" y="20" width="90" height="60" rx="8" fill="white" fill-opacity="0.8"
                            stroke="#181242" stroke-opacity="0.25" />
                        <rect x="55" y="40" width="145" height="60" rx="8" fill="white" fill-opacity="0.9"
                            stroke="#2a215e" stroke-opacity="0.25" />
                        <path d="M65 85L90 60L110 75L140 50L180 85H65Z" fill="#181242" fill-opacity="0.15" />
                        <circle cx="95" cy="50" r="8" fill="#2a215e" fill-opacity="0.2" />
                    </svg>
                </div>
            </div>
        </div>
        <div style="text-align:center; margin-top:14px; color:#94A3B8; font-size:12px;">
            رمز الخطأ: 404 · {{ now()->format('Y-m-d H:i') }}
        </div>
    </main>
</body>

</html>
