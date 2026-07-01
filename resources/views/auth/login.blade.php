<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>تسجيل الدخول - ApGroup</title>
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <style>
    *, *::before, *::after, body, html {
      font-family: 'Alexandria', 'Open Sans', sans-serif !important;
    }
  </style>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- CSS Files -->
  @vite([
    'resources/assets/css/nucleo-icons.css',
    'resources/assets/css/nucleo-svg.css',
    'resources/assets/css/soft-ui-dashboard.css',
  ])
</head>

<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg blur blur-rounded top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="/">
              ApGroup Dashboard
            </a>
            <div class="collapse navbar-collapse" id="navigation">
              <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                  <a class="nav-link me-2" href="/">
                    الرئيسية
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link me-2" href="{{ route('login') }}">
                    تسجيل الدخول
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- End Navbar -->
      </div>
    </div>
  </div>
  <main class="main-content mt-0">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
              <div class="card card-plain mt-8">
                <div class="card-header pb-0 text-start bg-transparent">
                  <h3 class="font-weight-bolder text-info text-gradient">مرحباً بك مجدداً</h3>
                  <p class="mb-0 text-secondary text-xs">أدخل اسم المستخدم وكلمة المرور لتسجيل الدخول</p>
                </div>
                <div class="card-body">
                  
                  {{-- Session Status --}}
                  @if (session('status'))
                      <div class="alert alert-success text-white text-xs mb-3" role="alert">
                          {{ session('status') }}
                      </div>
                  @endif

                  <form role="form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <label class="text-xs font-weight-bold text-secondary">اسم المستخدم</label>
                    <div class="mb-3">
                      <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="اسم المستخدم" aria-label="Username" required autofocus autocomplete="username">
                      @error('username')
                        <div class="text-danger text-xs mt-1">{{ $message }}</div>
                      @enderror
                    </div>

                    <label class="text-xs font-weight-bold text-secondary">كلمة المرور</label>
                    <div class="mb-3">
                      <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="كلمة المرور" aria-label="Password" required autocomplete="current-password">
                      @error('password')
                        <div class="text-danger text-xs mt-1">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="form-check form-switch text-end" style="direction: ltr !important;">
                      <input class="form-check-input me-auto ms-2" type="checkbox" id="remember_me" name="remember">
                      <label  class="form-check-label text-xs" for="remember_me">تذكرني على هذا الجهاز</label>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">تسجيل الدخول</button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
            <div class="col-md-6">
              <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('{{ asset('assets/img/curved-images/curved6.jpg') }}')"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer py-5">
    <div class="container">
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0 text-secondary">
          </p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Core JS Files -->
  @vite([
    'resources/assets/js/core/popper.min.js',
    'resources/assets/js/core/bootstrap.min.js',
    'resources/assets/js/plugins/perfect-scrollbar.min.js',
    'resources/assets/js/plugins/smooth-scrollbar.min.js',
    'resources/assets/js/soft-ui-dashboard.min.js'
  ])
</body>

</html>
