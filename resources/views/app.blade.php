<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title inertia>{{ config('app.name', 'SIMS') }}</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
    <script>
        window.onerror = function(msg, src, line, col, err) {
            document.body.innerHTML += '<pre style="background:red;color:white;padding:20px;position:fixed;top:0;left:0;right:0;z-index:9999;font-size:14px;white-space:pre-wrap;">'
                + 'JS ERROR:\n' + msg + '\n\nSource: ' + src + ':' + line
                + (err ? '\n\nStack:\n' + err.stack : '') + '</pre>';
        };
        window.addEventListener('unhandledrejection', function(e) {
            document.body.innerHTML += '<pre style="background:orange;color:black;padding:20px;position:fixed;top:0;left:0;right:0;z-index:9999;font-size:14px;white-space:pre-wrap;">'
                + 'UNHANDLED PROMISE REJECTION:\n' + (e.reason?.message || e.reason) 
                + (e.reason?.stack ? '\n\nStack:\n' + e.reason.stack : '') + '</pre>';
        });
    </script>
</body>
</html>
