@php
    static $cachedManifest = null;
    $entries = ['resources/css/app.css', 'resources/js/app.js'];

    if ($cachedManifest === null) {
        foreach ([
            public_path('build/manifest.json'),
            base_path('api/build/manifest.json'),
        ] as $manifestPath) {
            if (is_file($manifestPath)) {
                $cachedManifest = json_decode((string) file_get_contents($manifestPath), true);
                break;
            }
        }
    }

    $manifestData = $cachedManifest;
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
@elseif (! (env('VERCEL') || env('VERCEL_ENV')))
    @vite($entries)
@endif
