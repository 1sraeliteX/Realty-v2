<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,
          initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont,
                         'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 2rem;
            max-width: 480px;
        }
        .code {
            font-size: 6rem;
            font-weight: 800;
            color: #f59e0b;
            line-height: 1;
        }
        h1 {
            font-size: 1.5rem;
            margin: 1rem 0 0.5rem;
            color: #f1f5f9;
        }
        p {
            color: #94a3b8;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        a {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background: #3b82f6;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        a:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">404</div>
        <h1>Page Not Found</h1>
        <p>The page you're looking for doesn't exist
             or has been moved.</p>
        <a href="/admin/dashboard">Go to Dashboard</a>
    </div>
</body>
</html>
