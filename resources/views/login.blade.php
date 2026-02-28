<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        :root {
            --bg: #0a1020;
            --panel: #131d33;
            --line: #2f3b60;
            --text: #f7f9ff;
            --sub: #b8c6ea;
            --brand: #5ff7c8;
            --danger: #ff8695;
        }

        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 15% 20%, #243968 0%, var(--bg) 60%);
            color: var(--text);
            padding: 16px;
        }

        .card {
            width: min(440px, 100%);
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 14px;
            padding: 24px;
        }

        h1 {
            margin: 0 0 6px 0;
            font-size: 26px;
        }

        p {
            margin: 0 0 20px 0;
            color: var(--sub);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 11px;
            margin-bottom: 14px;
            background: #0c1325;
            color: var(--text);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            color: var(--sub);
            font-size: 14px;
        }

        .remember input {
            width: auto;
            margin: 0;
        }

        button {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            color: #063424;
            background: var(--brand);
        }

        .links {
            margin-top: 14px;
            font-size: 14px;
            color: var(--sub);
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        a {
            color: #9fd5ff;
            text-decoration: none;
        }

        .error {
            border-radius: 10px;
            padding: 11px;
            margin-bottom: 14px;
            background: rgba(255, 134, 149, 0.14);
            border: 1px solid rgba(255, 134, 149, 0.45);
            color: #ffcad1;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Login Admin</h1>
        <p>Masuk untuk mengelola antrian review.</p>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <label class="remember" for="remember">
                <input id="remember" type="checkbox" name="remember" value="1">
                Ingat saya
            </label>

            <button type="submit">Masuk</button>
        </form>

        <div class="links">
            <a href="{{ route('review.create') }}">Halaman Publik</a>
            @if (config('auth.allow_registration'))
                <a href="{{ route('register') }}">Daftar Admin Baru</a>
            @endif
        </div>
    </main>
</body>
</html>
