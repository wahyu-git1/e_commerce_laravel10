<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>TokoLaravel: Official Site</title>

        <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="{{ asset('resources/views/themes/shop/assets/js/main.js') }}"></script> -->
   <!-- untuk sementara main.js nya ditaruh di public -->
    <script src="{{ asset('themes/indotoko/assets/js/main.js') }}"></script>

    @vite([
        'resources/sass/app.scss', 
        'resources/js/app.js', 

        'resources/views/themes/shop/assets/css/main.css',
        'resources/views/themes/shop/assets/plugins/jqueryui/jquery-ui.css',

        'resources/views/themes/shop/assets/plugins/jqueryui/jquery-ui.min.js',
        'resources/views/themes/shop/assets/js/main.js',

        ])

    <title>IndoToko: Official Site</title>
</head>
<body>

    @include('themes.shop.shared.header')
    @yield('content')
    @include('themes.shop.shared.footer')

    <!-- <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>     -->
    <script>
    $(document).ready(function () {
        console.log("jQuery bekerja!");
    });
    console.log("Main.js successfully loaded!");

</script>
</body>
</html>