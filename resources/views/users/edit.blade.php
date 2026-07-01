@extends('layout.app')

@section('content')
<div class="container-fluid py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-12">
      
      <div class="card mb-4">
        <div class="card-header pb-0">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">تعديل بيانات المستخدم: {{ $user->name }}</h6>
              <p class="text-xs text-secondary mb-0">قم بتحديث معلومات الحساب أو الأدوار الوظيفية المخصصة له</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary mb-0">
              <i class="bi bi-arrow-right"></i> العودة
            </a>
          </div>
        </div>

        <div class="card-body mt-3">
          <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
              {{-- Name --}}
              <div class="col-md-6 col-12 mb-3">
                <label for="name" class="form-label text-xs font-weight-bold text-secondary">الاسم الكامل</label>
                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="أدخل اسم المستخدم">
                @error('name')
                  <div class="invalid-feedback text-xs">{{ $message }}</div>
                @enderror
              </div>

              {{-- Email --}}
              <div class="col-md-6 col-12 mb-3">
                <label for="email" class="form-label text-xs font-weight-bold text-secondary">البريد الإلكتروني</label>
                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="name@example.com">
                @error('email')
                  <div class="invalid-feedback text-xs">{{ $message }}</div>
                @enderror
              </div>

              {{-- Password (Optional) --}}
              <div class="col-md-6 col-12 mb-3">
                <label for="password" class="form-label text-xs font-weight-bold text-secondary">كلمة المرور الجديدة (اختياري)</label>
                <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" placeholder="اتركها فارغة لعدم تغييرها">
                @error('password')
                  <div class="invalid-feedback text-xs">{{ $message }}</div>
                @enderror
              </div>

              {{-- Password Confirmation --}}
              <div class="col-md-6 col-12 mb-4">
                <label for="password_confirmation" class="form-label text-xs font-weight-bold text-secondary">تأكيد كلمة المرور</label>
                <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" placeholder="أعد إدخال كلمة المرور">
              </div>
            </div>

            <hr class="horizontal dark">

            {{-- Assign Roles --}}
            <div class="mb-4">
              <label class="form-label text-xs font-weight-bold text-secondary d-block">تعيين الدور الوظيفي (Roles)</label>
              <div class="row">
                @foreach($roles as $role)
                  <div class="col-md-4 col-6 mb-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                      <label class="form-check-label text-sm text-dark font-weight-bold" for="role-{{ $role->id }}">
                        {{ $role->name }}
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-sm bg-gradient-primary mb-0 px-4">
                <i class="bi bi-check2-circle me-1"></i> حفظ وتحديث البيانات
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
