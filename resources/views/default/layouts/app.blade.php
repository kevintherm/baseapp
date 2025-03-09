<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @if (isset($head))
        {!! $head !!}
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.9.2/tailwind.min.css">

    @if (isset($css))
        <style>{!! $css !!}</style>
    @endif
</head>
<body>
    {!! $slot !!}

    @if (isset($foot))
        {{ $foot }}
    @endif

    @if (isset($js))
        {!! $js !!}
    @endif
</body>
</html>
