@extends('layout.app')

@section('content')
<div class="container-fluid py-4">

  {{-- Session Notifications --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
      <span class="alert-icon"><i class="bi bi-check-circle"></i></span>
      <span class="alert-text"><strong>نجاح!</strong> {{ session('success') }}</span>
      <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
      <span class="alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
      <span class="alert-text"><strong>تنبيه!</strong> {{ session('error') }}</span>
      <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  {{-- Tabs Navigation --}}
  <div class="nav-wrapper position-relative end-0 mb-4" style="direction: ltr;">
    <ul class="nav nav-pills nav-fill p-1 bg-white border-radius-lg" role="tablist">
      <li class="nav-item">
        <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#users-tab" role="tab" aria-controls="users-tab" aria-selected="true">
          <i class="bi bi-people me-1"></i> إدارة المستخدمين
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#roles-tab" role="tab" aria-controls="roles-tab" aria-selected="false">
          <i class="bi bi-shield-lock me-1"></i> صلاحيات الأدوار (Roles)
        </a>
      </li>
    </ul>
  </div>

  <div class="tab-content">
    
    {{-- TAB 1: USERS --}}
    <div class="tab-pane fade show active" id="users-tab" role="tabpanel">
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">قائمة المستخدمين</h6>
            <p class="text-xs text-secondary mb-0">يمكنك هنا إضافة مستخدمين جدد وتعديل صلاحياتهم أو حذفهم</p>
          </div>
          <a href="{{ route('users.create') }}" class="btn btn-sm bg-gradient-primary mb-0">
            <i class="bi bi-person-plus me-1"></i> مستخدم جديد
          </a>
        </div>
        
        <div class="card-body px-0 pt-0 pb-2 mt-3">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">المستخدم</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">البريد الإلكتروني</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الدور المخصص</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ التسجيل</th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                  <tr>
                    <td>
                      <div class="d-flex px-3 py-1">
                        <div class="avatar avatar-sm bg-gradient-secondary me-3 d-flex align-items-center justify-content-center border-radius-md" style="width: 36px; height: 36px;">
                          <span class="text-white text-xs font-weight-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                          @if($user->id === auth()->id())
                            <span class="badge badge-sm bg-gradient-success text-xxs" style="font-size: 0.65rem; width: fit-content; padding: 2px 6px; margin-top: 2px;">أنت حالياً</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      @forelse($user->roles as $role)
                        <span class="badge badge-sm bg-gradient-info">{{ $role->name }}</span>
                      @empty
                        <span class="badge badge-sm bg-gradient-light text-secondary">بدون دور</span>
                      @endforelse
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at ? $user->created_at->format('Y-m-d') : '—' }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <a href="{{ route('users.edit', $user->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user" style="margin-left: 15px;">
                        <i class="bi bi-pencil-square"></i> تعديل
                      </a>
                      @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المستخدم؟')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="border-0 bg-transparent text-danger font-weight-bold text-xs p-0">
                            <i class="bi bi-trash"></i> حذف
                          </button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- TAB 2: ROLES --}}
    <div class="tab-pane fade" id="roles-tab" role="tabpanel">
      <div class="row">
        
        @foreach($roles as $role)
          <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card">
              <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="mb-0 text-capitalize">دور: {{ $role->name }}</h6>
                  <span class="badge bg-gradient-primary text-xxs">مجموع الصلاحيات: {{ $role->permissions->count() }}</span>
                </div>
                <p class="text-xs text-secondary mt-1">قم بتحديد أو إلغاء الصلاحيات المخصصة لهذا الدور</p>
              </div>
              <div class="card-body">
                <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
                  @csrf
                  
                  <div class="list-group list-group-flush mb-3">
                    @foreach($permissions as $permission)
                      <div class="list-group-item border-0 d-flex justify-content-between align-items-center px-0 py-2">
                        <div class="form-check form-switch ps-0">
                          <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}-perm-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                          <label class="form-check-label text-sm text-dark font-weight-bold ms-3" for="role-{{ $role->id }}-perm-{{ $permission->id }}">
                            {{ $permission->name }}
                          </label>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <button type="submit" class="btn btn-sm bg-gradient-dark w-100 mb-0">
                    <i class="bi bi-check-all"></i> حفظ صلاحيات {{ $role->name }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach

      </div>
    </div>

  </div>
</div>
@endsection
