<!doctype html>
<html lang="{{ app()->getLocale() }}"">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- csrf token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Trade Glazing Direct</title>
        <!-- styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('manifest.json') }}" rel="manifest">
    </head>
    <body>
        @if( isset($authorised) )
            <script>
                var authorised = {{($authorised) ? 'true' : 'false'}}
                var typesPermitted = {!! json_encode($permittedTypes) !!}
            </script>
        @endif
        @if( isset($data) )
            <script>var php_data = '{!! json_encode($data) !!}'</script>
        @endif
        @if( isset($message) )
            <script>var message = "{!! $message !!}"</script>
        @endif

        
        <div id="sales-app"></div>
        <script src="{{ asset('js/setup_idb.js') }}"></script>
        <script src="{{ asset('js/setup_service_worker.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>