<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan</title>
    <style>
        :root {
            --bg: #2b2b2b;
            /* abu-abu tua */
            --fg: #f5f5f5;
            --muted: #bdbdbd;
        }

        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--fg);
            font: 16px/1.5 system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, "Helvetica Neue", Arial;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .wrap {
            padding: 4rem 2rem;
            max-width: 900px;
        }

        .code {
            font-weight: 900;
            letter-spacing: .04em;
            margin: 0;
            font-size: clamp(64px, 16vw, 200px);
            line-height: .9;
            color: #fff;
        }

        .title {
            margin: .25rem 0 0;
            font-size: clamp(18px, 3vw, 28px);
            font-weight: 700;
        }

        .desc {
            margin: .5rem auto 2rem;
            color: var(--muted);
            max-width: 640px;
        }

        .btn {
            display: inline-block;
            padding: .9rem 1.3rem;
            border-radius: 10px;
            color: #000;
            background: #fff;
            text-decoration: none;
            font-weight: 700;
            border: 1px solid #e5e5e5;
        }

        .btn:hover {
            background: #e5e5e5;
        }

        .btn:active {
            transform: translateY(1px);
        }
    </style>
</head>

<body>
    <main class="wrap" role="main" aria-labelledby="code">
        <h1 class="code" id="code">404</h1>
        <p class="title">Halaman Tidak Ditemukan</p>
        <p class="desc">Maaf, alamat yang kamu tuju tidak tersedia atau telah dipindahkan.</p>
        <a class="btn" href="/">Persandian Diskominfos Prov Bali</a>
    </main>
</body>

</html>
