@php
    $entries = ['resources/css/app.css', 'resources/js/app.js'];
    $manifestPath = public_path('build/manifest.json');

    if (! file_exists($manifestPath) && (env('VERCEL') || env('VERCEL_ENV'))) {
        $manifestUrl = rtrim((string) config('app.url'), '/').'/build/manifest.json';
        $manifest = @file_get_contents($manifestUrl);
        $manifestData = $manifest ? json_decode($manifest, true) : null;
    } else {
        $manifestData = null;
    }
@endphp

@if ($manifestData)
    @foreach ($entries as $entry)
        @isset($manifestData[$entry])
            @php($chunk = $manifestData[$entry])
            @foreach ($chunk['css'] ?? [] as $css)
                <link rel="stylesheet" href="{{ asset('build/'.$css) }}">
            @endforeach
            @isset($chunk['file'])
                @if (str_ends_with($chunk['file'], '.css'))
                    <link rel="stylesheet" href="{{ asset('build/'.$chunk['file']) }}">
                @elseif (str_ends_with($chunk['file'], '.js'))
                    <script type="module" src="{{ asset('build/'.$chunk['file']) }}"></script>
                @endif
            @endisset
        @endisset
    @endforeach
@else
    @vite($entries)
@endif
