<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overlay Antrian Review</title>
    <style>
        :root {
            --text: #f6f8ff;
            --sub: #dbe6ff;
            --line: rgba(166, 196, 255, 0.25);
            --panel: rgba(5, 18, 44, 0.72);
            --accent: #45f1bf;
        }

        * {
            box-sizing: border-box;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
        }

        body {
            margin: 0;
            background: transparent;
            color: var(--text);
            padding: 18px;
        }

        .overlay {
            width: min(850px, 100%);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(130deg, rgba(15, 33, 74, 0.88), var(--panel));
            box-shadow: 0 25px 55px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(5px);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .title {
            font-size: 28px;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .count {
            font-size: 15px;
            color: var(--sub);
        }

        .queue-list {
            display: grid;
            gap: 8px;
        }

        .item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            align-items: start;
            padding: 12px;
            border-radius: 12px;
            background: rgba(5, 14, 35, 0.6);
            border: 1px solid rgba(118, 154, 231, 0.2);
        }

        .no {
            min-width: 52px;
            text-align: center;
            padding: 6px 8px;
            border-radius: 8px;
            background: rgba(69, 241, 191, 0.15);
            border: 1px solid rgba(69, 241, 191, 0.6);
            color: #9dffdf;
            font-weight: 700;
        }

        .name {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 2px 0;
            word-break: break-word;
        }

        .meta {
            margin: 0 0 4px 0;
            color: #c8dafd;
            font-size: 14px;
        }

        .message {
            margin: 0;
            color: #edf2ff;
            font-size: 17px;
            line-height: 1.3;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .empty {
            border-radius: 12px;
            padding: 20px;
            border: 1px dashed rgba(182, 205, 251, 0.45);
            text-align: center;
            color: #d6e2ff;
            background: rgba(0, 0, 0, 0.2);
        }

        .overflow {
            margin-top: 10px;
            color: #d2ddfa;
            font-size: 13px;
        }

        .toasts {
            position: fixed;
            top: 18px;
            right: 18px;
            width: min(360px, 90vw);
            display: grid;
            gap: 8px;
            pointer-events: none;
        }

        .toast {
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(69, 241, 191, 0.7);
            background: rgba(8, 19, 48, 0.95);
            color: #dcfff3;
            font-size: 14px;
            transform: translateY(-8px);
            opacity: 0;
            animation: toast-in 220ms ease forwards;
        }

        @keyframes toast-in {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <section class="overlay">
        <header class="header">
            <h1 class="title">Antrian Review</h1>
            <div id="queue-count" class="count">Total: 0</div>
        </header>

        <div id="queue-list" class="queue-list">
            <div class="empty">Belum ada yang masuk antrian.</div>
        </div>

        <div id="queue-overflow" class="overflow"></div>
    </section>

    <div id="toasts" class="toasts"></div>

    <script>
        const queueList = document.getElementById('queue-list');
        const queueCount = document.getElementById('queue-count');
        const queueOverflow = document.getElementById('queue-overflow');
        const toastContainer = document.getElementById('toasts');

        let initialized = false;
        const seenSubmissionIds = new Set();
        let fetching = false;

        function escapeHtml(text) {
            return String(text ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showToast(name) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<strong>${escapeHtml(name)}</strong> ikut review`;
            toastContainer.appendChild(toast);

            window.setTimeout(() => {
                toast.remove();
            }, 2800);
        }

        function renderQueue(queue, total) {
            if (!Array.isArray(queue) || queue.length === 0) {
                queueList.innerHTML = '<div class="empty">Belum ada yang masuk antrian.</div>';
                queueCount.textContent = `Total: ${total ?? 0}`;
                queueOverflow.textContent = '';
                return;
            }

            queueList.innerHTML = queue.map((item) => {
                const uidText = item.uid ? `UID: ${escapeHtml(item.uid)}` : 'UID: -';

                return `
                    <article class="item">
                        <div class="no">#${item.queue_number}</div>
                        <div>
                            <p class="name">${escapeHtml(item.name)}</p>
                            <p class="meta">${uidText}</p>
                            <p class="message">${escapeHtml(item.message)}</p>
                        </div>
                    </article>
                `;
            }).join('');

            const safeTotal = Number(total || 0);
            queueCount.textContent = `Total: ${safeTotal}`;
            queueOverflow.textContent = safeTotal > 10
                ? `Ditampilkan 10 teratas dari ${safeTotal} total antrian.`
                : '';
        }

        function trackRecentSubmissions(recentSubmissions) {
            if (!Array.isArray(recentSubmissions)) {
                return;
            }

            if (!initialized) {
                recentSubmissions.forEach((submission) => {
                    seenSubmissionIds.add(submission.id);
                });
                return;
            }

            recentSubmissions.forEach((submission) => {
                if (!seenSubmissionIds.has(submission.id)) {
                    showToast(submission.name);
                }
                seenSubmissionIds.add(submission.id);
            });
        }

        async function fetchQueue() {
            if (fetching) {
                return;
            }

            fetching = true;

            try {
                const response = await fetch('/queue/data?ts=' + Date.now(), {
                    headers: {
                        Accept: 'application/json',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();

                renderQueue(payload.queue ?? [], payload.total ?? 0);
                trackRecentSubmissions(payload.recent_submissions ?? []);

                initialized = true;
            } catch (error) {
                // Silent fallback for overlay runtime.
            } finally {
                fetching = false;
            }
        }

        fetchQueue();
        window.setInterval(fetchQueue, 1500);
    </script>
</body>
</html>
