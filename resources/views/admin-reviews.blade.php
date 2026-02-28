<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Antrian Review</title>
    <style>
        :root {
            --bg: #0b1020;
            --panel: #121a30;
            --line: #293457;
            --text: #f5f8ff;
            --sub: #bbc7e7;
            --accent: #65ffc9;
            --danger: #ff7f8f;
        }

        * {
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            background: linear-gradient(145deg, #0f1730 0%, #0a1020 65%);
            padding: 22px;
        }

        .card {
            width: min(1200px, 100%);
            margin: 0 auto;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.25);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px;
            border-bottom: 1px solid var(--line);
            gap: 14px;
            flex-wrap: wrap;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .meta {
            color: var(--sub);
            font-size: 14px;
            margin-top: 4px;
        }

        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .actions a,
        .actions button {
            color: var(--text);
            text-decoration: none;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 12px;
            background: #10182b;
            cursor: pointer;
            font-size: 14px;
        }

        .actions button {
            border-color: rgba(255, 127, 143, 0.55);
            color: #ffc9d0;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid rgba(63, 82, 128, 0.45);
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            color: #d5e0ff;
            font-size: 13px;
            letter-spacing: 0.3px;
            background: rgba(4, 11, 25, 0.45);
        }

        td.message {
            white-space: pre-wrap;
            max-width: 520px;
            word-break: break-word;
        }

        .queue-no {
            color: var(--accent);
            font-weight: 700;
        }

        .delete {
            border: 1px solid rgba(255, 127, 143, 0.75);
            color: #ffcbd2;
            background: rgba(255, 127, 143, 0.08);
            border-radius: 8px;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 13px;
        }

        .empty {
            color: var(--sub);
            text-align: center;
            padding: 22px;
        }

        .badge {
            display: inline-block;
            margin-left: 8px;
            font-size: 12px;
            background: rgba(101, 255, 201, 0.15);
            color: #a1ffe0;
            border: 1px solid rgba(101, 255, 201, 0.5);
            border-radius: 999px;
            padding: 3px 8px;
        }
    </style>
</head>
<body>
    <main class="card">
        <header class="header">
            <div>
                <h1>Dashboard Admin Review</h1>
                <div class="meta">
                    Data refresh otomatis tiap 2 detik.
                    <span class="badge" id="total-badge">Total: 0</span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('review.create') }}">Halaman Publik</a>
                <a href="{{ route('overlay.index') }}" target="_blank" rel="noreferrer">Overlay</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </header>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No Antrian</th>
                        <th>Nama</th>
                        <th>UID</th>
                        <th>Pesan</th>
                        <th>Waktu Masuk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="queue-body">
                    <tr>
                        <td class="empty" colspan="6">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const queueBody = document.getElementById('queue-body');
        const totalBadge = document.getElementById('total-badge');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let isLoading = false;

        function escapeHtml(text) {
            return String(text ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDate(isoString) {
            if (!isoString) {
                return '-';
            }

            const date = new Date(isoString);
            if (Number.isNaN(date.getTime())) {
                return '-';
            }

            return date.toLocaleString('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'medium',
            });
        }

        function renderRows(reviews) {
            if (!Array.isArray(reviews) || reviews.length === 0) {
                queueBody.innerHTML = '<tr><td class="empty" colspan="6">Belum ada antrian review.</td></tr>';
                return;
            }

            queueBody.innerHTML = reviews.map((review) => {
                const uid = review.uid ? escapeHtml(review.uid) : '-';

                return `
                    <tr>
                        <td class="queue-no">#${review.queue_number}</td>
                        <td>${escapeHtml(review.name)}</td>
                        <td>${uid}</td>
                        <td class="message">${escapeHtml(review.message)}</td>
                        <td>${formatDate(review.created_at)}</td>
                        <td>
                            <button class="delete" data-id="${review.id}">Selesai / Hapus</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function loadReviews() {
            if (isLoading) {
                return;
            }

            isLoading = true;

            try {
                const response = await fetch('/admin/reviews/data?ts=' + Date.now(), {
                    headers: {
                        Accept: 'application/json',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const total = Number(payload.total || 0);

                totalBadge.textContent = `Total: ${total}`;
                renderRows(payload.reviews ?? []);
            } catch (error) {
                // ignore intermittent errors and keep previous data on screen.
            } finally {
                isLoading = false;
            }
        }

        async function deleteReview(reviewId) {
            const response = await fetch(`/admin/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('delete failed');
            }
        }

        queueBody.addEventListener('click', async (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement) || !target.classList.contains('delete')) {
                return;
            }

            const reviewId = target.getAttribute('data-id');
            if (!reviewId) {
                return;
            }

            target.setAttribute('disabled', 'disabled');

            try {
                await deleteReview(reviewId);
                await loadReviews();
            } catch (error) {
                target.removeAttribute('disabled');
            }
        });

        loadReviews();
        window.setInterval(loadReviews, 2000);
    </script>
</body>
</html>
