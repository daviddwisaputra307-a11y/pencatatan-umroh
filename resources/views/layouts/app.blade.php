<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pencatatan Umroh')</title>

    {{-- Tailwind CDN biar sat-set --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body{
            background: radial-gradient(1200px 700px at 30% 10%, rgba(37,99,235,.35), transparent 60%),
                        radial-gradient(1000px 700px at 80% 30%, rgba(16,185,129,.28), transparent 60%),
                        radial-gradient(900px 600px at 60% 90%, rgba(168,85,247,.22), transparent 60%),
                        #050814;
            color: rgba(255,255,255,.92);
        }
        .glass{
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 18px 60px rgba(0,0,0,.45);
        }
        .soft-border{
            border: 1px solid rgba(255,255,255,.10);
        }
        .pill{
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.10);
        }
        .table-row{
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.06);
        }
        .table-row:hover{
            background: rgba(255,255,255,.06);
        }
        .btn{
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.06);
        }
        .btn:hover{ background: rgba(255,255,255,.10); }
        .btn-primary{
            border: 1px solid rgba(59,130,246,.45);
            background: rgba(59,130,246,.22);
        }
        .btn-primary:hover{ background: rgba(59,130,246,.30); }
        .btn-danger{
            border: 1px solid rgba(239,68,68,.45);
            background: rgba(239,68,68,.16);
        }
        .btn-danger:hover{ background: rgba(239,68,68,.22); }
        input, select{
            background: rgba(255,255,255,.06) !important;
            border: 1px solid rgba(255,255,255,.12) !important;
            color: rgba(255,255,255,.92) !important;
        }
        input::placeholder{ color: rgba(255,255,255,.45); }
        select option{ color: #0b1020; } /* biar dropdown kebaca */
        .hint{ color: rgba(255,255,255,.55); }
        .err{ color: rgba(248,113,113,.95); }
    </style>
</head>

<body class="min-h-screen">
    <div class="max-w-5xl mx-auto px-4 py-10">
        @yield('content')
    </div>
</body>
</html>
