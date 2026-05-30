<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out&hellip;</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: #ecf0f5;
            color: #444;
        }
        .msg { text-align: center; }
        .msg p { margin-top: 12px; font-size: 15px; }
    </style>
</head>
<body>
    <div class="msg">
        <p>Logging out&hellip;</p>
    </div>

    {{--
        An <img> request carries the browser's cookies (same host: 127.0.0.1)
        and is not subject to X-Frame-Options or CORS read restrictions.
        OrangeHRM receives its own session cookie, clears the session, and
        returns a redirect — either onload or onerror fires once done.
    --}}
    <img src="{{ $orangehrmLogoutUrl }}"
         style="display:none;"
         onload="redirectToLogin()"
         onerror="redirectToLogin()">

    <script>
        var _redirected = false;

        function redirectToLogin() {
            if (_redirected) return;
            _redirected = true;
            window.location.href = '{{ $snipeitLoginUrl }}';
        }

        // Fallback: redirect after 3 s if neither onload nor onerror fires
        setTimeout(redirectToLogin, 3000);
    </script>
</body>
</html>
