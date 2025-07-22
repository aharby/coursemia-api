<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - Coursemia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Open Graph for better previews -->
    <meta property="og:title" content="{{ $title }} - Coursemia">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Coursemia">

    <style>
        :root {
            --text-color: #333;
            --bg-color: #fff;
            --accent-color: #2c3e50;
            --max-width: 800px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg-color);
            color: var(--text-color);
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            line-height: 1.8;
            font-size: 16px;
        }

        .container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        h1 {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        h2, h3 {
            margin-top: 2rem;
            color: var(--accent-color);
        }

        p {
            margin: 1rem 0;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 4rem;
            font-size: 0.9rem;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 1rem;
            text-align: center;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #121212;
                --text-color: #eee;
                --accent-color: #FF1D5C;
            }

            footer {
                color: #aaa;
                border-top-color: #444;
            }
        }

        .header-flex {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            /* flex-wrap: wrap; */
            margin-bottom: 0;
        }

        .header-flex h1 {
            font-size: 2rem;
            color: var(--accent-color);
            margin: 0;
        }

        .logo {
            height: 100px;
            width: auto;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header-flex">
            <h1>{{ $title }}</h1>
            <img src="{{ asset('images/logo.png') }}" alt="Coursemia Logo" class="logo">
        </div>

        {{-- This will render HTML content from the backend --}}
        {!! $content !!}

        <footer>
            &copy; {{ date('Y') }} Coursemia. All rights reserved.
        </footer>
    </div>
</body>
</html>
