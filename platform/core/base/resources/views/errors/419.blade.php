<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>{{ __('An Error Occurred: Token mismatch') }}</title>
    <style>
        body {
            background-color: #fff;
            color: #222;
            font: 16px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
        }
        .container {
            margin: 30px;
            max-width: 600px;
        }
        h1 {
            color: #dc3545;
            font-size: 24px;
        }

        h2 {
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ __('Oops! The session has expired. ') }}</h1>
    <h2>{{ __('The session has expired please reload the page.".') }}</h2>
    <h2><a href="{{ url()->current() }}" class="btn btn-primary mt-3">Refresh</a></h2>
</div>
</body>
</html>
