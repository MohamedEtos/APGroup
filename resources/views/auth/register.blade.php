<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>إنشاء حساب - ApGroup</title>
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <style>
    *, *::before, *::after, body, html {
      font-family: 'Alexandria', 'Open Sans', sans-serif !important;
    }
  </style>

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

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
                  <a class="nav-link me-2" href="{{ route('register') }}">
                    إنشاء حساب جديد
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
                  <h3 class="font-weight-bolder text-info text-gradient">إنشاء حساب جديد</h3>
                  <p class="mb-0 text-secondary text-xs">أدخل تفاصيل بياناتك الشخصية لإنشاء حسابك</p>
                </div>
                <div class="card-body">
                  <form role="form" method="POST" action="{{ route('register') }}">
                    @csrf

                    <label class="text-xs font-weight-bold text-secondary">الاسم الكامل</label>
                    <div class="mb-3">
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="الاسم الكامل" aria-label="Name" required autofocus autocomplete="name">
                      @error('name')
                        <div class="text-danger text-xs mt-1">{{ $message }}</div>
                      @enderror
                    </div>

                    <label class="text-xs font-weight-bold text-secondary">البريد الإلكتروني</label>
                    <div class="mb-3">
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="البريد الإلكتروني" aria-label="Email" required autocomplete="username">
                      @error('email')
                        <div class="text-danger text-xs mt-1">{{ $message }}</div>
                      @enderror
                    </div>

                    <label class="text-xs font-weight-bold text-secondary">كلمة المرور</label>
                    <div class="mb-3">
                      <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="كلمة المرور" aria-label="Password" required autocomplete="new-password">
                      @error('password')
                        <div class="text-danger text-xs mt-1">{{ $message }}</div>
                      @enderror
                    </div>

                    <label class="text-xs font-weight-bold text-secondary">تأكيد كلمة المرور</label>
                    <div class="mb-3">
                      <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="تأكيد كلمة المرور" aria-label="Confirm Password" required autocomplete="new-password">
                      @error('password_confirmation')
                        <div class="text-danger text-xs mt-1">{{ $message }}</div>
                      @enderror
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">إنشاء حساب</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-info text-gradient font-weight-bold">تسجيل الدخول</a>
                  </p>
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
            Copyright © <script>document.write(new Date().getFullYear())</script> Soft by Creative Tim.
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
