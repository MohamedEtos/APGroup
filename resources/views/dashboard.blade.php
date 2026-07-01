@extends('layout.app')

@section('content')


    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">إجمالي الفواتير</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalInvoices ?? 0) }}
                      <span class="text-secondary text-xs font-weight-normal">فاتورة</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-start">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">الكمية المطلوبة (توب)</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalQty ?? 0, 3) }}
                      <span class="text-secondary text-xs font-weight-normal">رول/قطعة</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-start">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-box-2 text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">الوزن المطلوب الإجمالي</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalKg ?? 0, 3) }}
                      <span class="text-secondary text-xs font-weight-normal">كيلو</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-start">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-bold-down text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">الوزن المسلم الإجمالي</p>
                    <h5 class="font-weight-bolder mb-0">
                      {{ number_format($totalDeliveredKg ?? 0, 3) }}
                      <span class="text-secondary text-xs font-weight-normal">كيلو</span>
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-start">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-delivery-fast text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-lg-5 mb-lg-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="bg-gradient-dark border-radius-lg py-3 pe-1 mb-3">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170px"></canvas>
                </div>
              </div>
              <h6 class="ms-2 mt-4 mb-0"> توزيع الأقمشة الأكثر طلباً </h6>
              <p class="text-sm ms-2">أعلى 4 أنواع أقمشة تم شحنها واستهلاكها (بالكيلو)</p>
              <div class="container border-radius-lg">
                <div class="row">
                  @forelse(($topFabrics ?? collect())->take(4) as $fabric)
                    @php
                      $maxKg = ($topFabrics ?? collect())->first()->total_kg ?? 1;
                      $percentage = min(100, round(($fabric->total_kg / $maxKg) * 100));
                    @endphp
                    <div class="col-3 py-3 ps-0">
                      <div class="d-flex mb-2">
                        <p class="text-xs mt-1 mb-0 font-weight-bold text-truncate" title="{{ $fabric->type }}">{{ $fabric->type }}</p>
                      </div>
                      <h5 class="font-weight-bolder mb-1">{{ number_format($fabric->total_kg, 1) }}</h5>
                      <div class="progress w-75">
                        <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  @empty
                    <div class="col-12 py-3 text-center text-secondary text-xs">لا توجد بيانات للأقمشة بعد.</div>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header pb-0">
              <h6>معدل طلب الأقمشة شهرياً</h6>
              <p class="text-sm">
                <i class="fas fa-chart-line text-success"></i>
                إجمالي الوزن المطلوب مقدراً  لكل شهر من شهور السنة الحالية
              </p>
            </div>
            <div class="card-body p-3">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="300px"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row my-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row mb-3">
                <div class="col-6">
                  <h6>أحدث الفواتير المسجلة</h6>
                  <p class="text-sm">
                    <i class="fas fa-check text-info" aria-hidden="true"></i>
                    آخر 6 فواتير تم إنشاؤها في النظام
                  </p>
                </div>
              </div>
            </div>
            <div class="card-body p-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-3">رقم الفاتورة</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-end pe-3">الموظف / العميل</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">إجمالي العدد</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">إجمالي الكيلو</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الحالة</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($latestInvoices ?? [] as $inv)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1 align-items-center">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm pe-2 text-primary font-weight-bold">{{ $inv->invoice_number }}</h6>
                            <span class="text-xs text-secondary pe-2">{{ $inv->date }}</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex flex-column justify-content-center text-end pe-3">
                          <span class="text-xs font-weight-bold text-dark">{{ $inv->sender }}</span>
                          <span class="text-xxs text-secondary">إلى: {{ $inv->receiver }}</span>
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold">{{ number_format($inv->total_qty, 3) }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold text-primary">{{ number_format($inv->total_kg, 3) }} كيلو</span>
                      </td>
                      <td class="align-middle text-center">
                        @if($inv->status === 'received')
                          <span class="badge badge-sm bg-gradient-success">تم الاستلام</span>
                        @else
                          <span class="badge badge-sm bg-gradient-warning">قيد الاستلام</span>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-xs text-secondary">لا توجد فواتير مسجلة بعد.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6>آخر عمليات الاستلام</h6>
              <p class="text-sm">
                <i class="fas fa-clock text-info" aria-hidden="true"></i>
                متابعة  للفواتير التي تم استلامها في المستودعات
              </p>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                @forelse($timelineInvoices ?? [] as $inv)
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-check-bold text-success text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">تم تأكيد استلام الفاتورة {{ $inv->invoice_number }}</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                      بواسطة: {{ $inv->receiver }} - الوزن: {{ number_format($inv->items->sum('delivered_total_kg'), 3) }} كيلو - {{ $inv->updated_at ? $inv->updated_at->diffForHumans() : $inv->date }}
                    </p>
                  </div>
                </div>
                @empty
                <div class="text-center py-4 text-xs text-secondary">لا توجد عمليات استلام مؤكدة بعد.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-end">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fas fa-heart"></i> by
                <a href="https://github.com/MohamedMahrous1" class="font-weight-bold" target="_blank">Mohamed Mahrous</a>
                for a better web.
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>


@endsection