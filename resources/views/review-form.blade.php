<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Review</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #0a111f;
            --panel: #151f34;
            --line: #2a3a61;
            --text: #f6f8ff;
            --sub: #b2c0df;
            --brand: #47e4ac;
            --danger: #ff6d81;
        }

        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at 20% 20%, #23305a 0%, #0a111f 60%);
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .card {
            width: min(700px, 100%);
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        h1 {
            margin: 0 0 8px 0;
            font-size: 28px;
        }

        p {
            margin: 0 0 20px 0;
            color: var(--sub);
        }

        .links {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .links a {
            color: var(--text);
            text-decoration: none;
            border: 1px solid var(--line);
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 13px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            background: #0f172a;
            color: var(--text);
            border-radius: 10px;
            padding: 12px;
            font-size: 15px;
            margin-bottom: 16px;
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        button {
            border: none;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            color: #03261a;
            background: var(--brand);
        }

        .alert {
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert.success {
            background: rgba(71, 228, 172, 0.15);
            border: 1px solid rgba(71, 228, 172, 0.5);
            color: #8dfdd1;
        }

        .alert.error {
            background: rgba(255, 109, 129, 0.15);
            border: 1px solid rgba(255, 109, 129, 0.45);
            color: #ffc7cf;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Kirim Review</h1>
        <p>Isi data kamu untuk masuk antrian review livestream.</p>

        <div class="links">
            <a href="{{ route('overlay.index') }}" target="_blank" rel="noreferrer">Buka Overlay</a>
            <a href="{{ route('admin.reviews.index') }}">Masuk Admin</a>
        </div>

        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('review.store') }}" method="POST">
            @csrf

            <label for="name">Nama</label>
            <input id="name" name="name" value="{{ old('name') }}" maxlength="120" required>

            <label for="uid">UID (opsional)</label>
            <input id="uid" name="uid" value="{{ old('uid') }}" maxlength="120">

            <label for="message">Pesan Review</label>
            <textarea id="message" name="message" maxlength="2000" required>{{ old('message') }}</textarea>

            <button type="submit">Kirim Ke Antrian</button>
        </form>
    </main>
</body>
</html>
