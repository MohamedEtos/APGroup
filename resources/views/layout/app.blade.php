
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/logos/ap.svg') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/logos/ap.svg') }}">
  <title>
    AP Group
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <style>
    *,
    *::before,
    *::after,
    body,
    html {
      font-family: 'Alexandria', 'Open Sans', sans-serif !important;
    }
  </style>
  <!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/9a4cf689a5.js" crossorigin="anonymous"></script>
  <!-- CSS Files -->
  @vite([
  'resources/assets/css/nucleo-icons.css',
  'resources/assets/css/nucleo-svg.css',
  'resources/assets/css/soft-ui-dashboard.css',
   ])
</head>

<body class="g-sidenav-show rtl bg-gray-100">
    @include('components.aside')
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg overflow-hidden">
        @include('components.nav')
        @yield('content')
    </main>

    @include('components.scripts')

</body>

</html>

