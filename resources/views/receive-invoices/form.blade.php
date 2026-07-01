@extends('layout.app')

@section('content')

  <div class="container-fluid py-4">

    {{-- Error Alerts --}}
    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
        <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
        <span class="alert-text"><strong>خطأ!</strong> يرجى تصحيح الأخطاء التالية:</span>
        <ul class="mb-0 mt-2 text-white">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    @vite('resources/css/sections/receive-invoices-form.css')

    <div class="row">
      
      {{-- LEFT COLUMN: Original requested invoice (Read Only) --}}
      <div class="col-lg-6 col-12 mb-4">
        <div class="card comparative-card original-section h-100">
          <div class="card-header bg-transparent pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0 text-dark">الفاتورة الأصلية (الكميات المطلوبة)</h6>
                <span class="text-xs text-secondary">رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong></span>
              </div>
              @if($invoice->img && $invoice->img !== 'assets/img/team-2.jpg')
                <img src="/{{ $invoice->img }}" class="img-preview-receipt" alt="صورة الفاتورة" onclick="openImageModal('/{{ $invoice->img }}')">
              @endif
            </div>
          </div>

          <div class="card-body mt-3">
            <div class="row mb-3">
              <div class="col-6">
                <span class="text-xs text-secondary font-weight-bold d-block">المستلم (العميل)</span>
                <span class="text-sm font-weight-bold text-dark">{{ $invoice->receiver }}</span>
              </div>
              <div class="col-6 text-start">
                <span class="text-xs text-secondary font-weight-bold d-block">تاريخ الإنشاء</span>
                <span class="text-sm font-weight-bold text-dark">{{ $invoice->date }}</span>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-xs text-secondary ps-2">#</th>
                    <th class="text-xs text-secondary ps-2">الكود</th>
                    <th class="text-xs text-secondary ps-2">النوع/اللون</th>
                    <th class="text-xs text-secondary text-center">العدد المطلوب</th>
                    <th class="text-xs text-secondary text-center">الوزن المطلوب</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($invoice->items as $idx => $item)
                    <tr>
                      <td class="text-xs font-weight-bold ps-2">{{ $idx + 1 }}</td>
                      <td class="text-xs font-weight-bold">
                        <span class="text-primary">{{ $item->code }}</span>
                      </td>
                      <td class="text-xs">
                        <span>{{ $item->type }}</span>
                        @if($item->fabric_color)
                          <br><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $item->fabric_color }};border:1px solid #ccc;vertical-align:middle;"></span> <small class="text-secondary">{{ $item->fabric_color }}</small>
                        @endif
                      </td>
                      <td class="text-xs text-center font-weight-bold">{{ number_format($item->qty, 3) }} ({{ $item->unit }})</td>
                      <td class="text-xs text-center text-primary font-weight-bold">{{ number_format($item->total_kg, 3) }} كيلو</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-4 p-3 bg-white border-radius-lg border d-flex justify-content-around text-center">
              <div>
                <span class="text-xs text-secondary font-weight-bold d-block">إجمالي العدد المطلوب</span>
                <span class="text-sm font-weight-bold text-dark">{{ number_format($invoice->total_qty, 3) }}</span>
              </div>
              <div class="vr bg-secondary opacity-25"></div>
              <div>
                <span class="text-xs text-secondary font-weight-bold d-block">إجمالي الوزن المطلوب</span>
                <span class="text-sm font-weight-bold text-primary">{{ number_format($invoice->total_kg, 3) }} كيلو</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT COLUMN: Actually received quantities (Input Form) --}}
      <div class="col-lg-6 col-12 mb-4">
        <div class="card comparative-card h-100">
          <form action="{{ route('receive-invoices.update', $invoice->id) }}" method="POST" class="h-100 d-flex flex-column">
            @csrf
            @method('PUT')

            <div class="card-header bg-transparent pb-0">
              <h6 class="mb-0 text-dark">الكميات المستلمة والمسلمة فعلياً</h6>
              <p class="text-xs text-secondary mb-0">قم بتأكيد الكميات المستلمة لكل صنف، وسيتم تمييز أي اختلاف بلون مختلف</p>
            </div>

            <div class="card-body mt-3 flex-grow-1">
              <div class="table-responsive">
                <table class="table align-items-center mb-0 delivery-table">
                  <thead>
                    <tr>
                      <th class="text-xs text-secondary ps-2" style="width:5%;">#</th>
                      <th class="text-xs text-secondary ps-2" style="width:25%;">الكود</th>
                      <th class="text-xs text-secondary text-center" style="width:35%;">العدد المستلم</th>
                      <th class="text-xs text-secondary text-center" style="width:35%;">الوزن المستلم</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($invoice->items as $idx => $item)
                      @php
                        // Check for old value on validation failure
                        $oldDeliveredQty = old("items.$idx.delivered_qty", $item->qty);
                        $oldDeliveredTotalKg = old("items.$idx.delivered_total_kg", $item->total_kg);
                      @endphp
                      <tr class="delivery-item-row" data-index="{{ $idx }}" data-req-qty="{{ $item->qty }}" data-req-kg="{{ $item->total_kg }}">
                        <td class="text-xs font-weight-bold ps-2 align-middle">
                          {{ $idx + 1 }}
                          <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                        </td>
                        <td class="text-xs font-weight-bold align-middle">
                          <span class="text-dark">{{ $item->code }}</span>
                          <br><span class="text-xxs text-secondary">{{ $item->unit }}</span>
                        </td>
                        <td class="align-middle">
                          <input type="number" name="items[{{ $idx }}][delivered_qty]"
                                 class="form-control form-control-sm text-center input-delivered-qty w-100"
                                 value="{{ $oldDeliveredQty }}" min="0" step="0.001" placeholder="0.000" required>
                          <span class="discrepancy-label text-center mt-1" id="qty-diff-{{ $idx }}">مختلف عن المطلوب!</span>
                        </td>
                        <td class="align-middle">
                          <input type="number" name="items[{{ $idx }}][delivered_total_kg]"
                                 class="form-control form-control-sm text-center input-delivered-kg w-100"
                                 value="{{ $oldDeliveredTotalKg }}" min="0" step="0.001" placeholder="0.000" required>
                          <span class="discrepancy-label text-center mt-1" id="kg-diff-{{ $idx }}">مختلف عن المطلوب!</span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              {{-- Received Summary --}}
              <div class="mt-4 p-3 bg-light border-radius-lg border d-flex justify-content-around text-center">
                <div>
                  <span class="text-xs text-secondary font-weight-bold d-block">إجمالي العدد المستلم</span>
                  <span class="text-sm font-weight-bold text-dark" id="total-received-qty">0</span>
                </div>
                <div class="vr bg-secondary opacity-25"></div>
                <div>
                  <span class="text-xs text-secondary font-weight-bold d-block">إجمالي الوزن المستلم</span>
                  <span class="text-sm font-weight-bold text-primary"><span id="total-received-kg">0.000</span> كيلو</span>
                </div>
              </div>
            </div>

            <div class="card-footer bg-transparent border-0 d-flex justify-content-end gap-2 py-3">
              <a href="{{ route('receive-invoices.index') }}" class="btn btn-sm btn-outline-secondary mb-0 py-2 px-3 font-weight-bold">
                إلغاء
              </a>
              <button type="submit" class="btn btn-sm bg-gradient-primary mb-0 py-2 px-4 font-weight-bold">
                <i class="fas fa-clipboard-check me-1"></i> تأكيد واستلام الفاتورة
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

  {{-- ===== Image Viewer Modal ===== --}}
  <div id="imageViewerModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.82);align-items:center;justify-content:center;" onclick="closeImageModal()">
    <div style="position:relative;" onclick="event.stopPropagation()">
      <button onclick="closeImageModal()" style="position:absolute;top:-13px;right:-13px;width:30px;height:30px;border-radius:50%;background:#fff;border:none;font-size:17px;line-height:1;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.3);">&times;</button>
      <img id="imageViewerModalImg" src="" style="max-width:90vw;max-height:88vh;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.5);display:block;">
    </div>
  </div>

  <script>
    function openImageModal(src) {
      document.getElementById('imageViewerModalImg').src = src;
      document.getElementById('imageViewerModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    function closeImageModal() {
      document.getElementById('imageViewerModal').style.display = 'none';
      document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeImageModal(); });

    document.addEventListener('DOMContentLoaded', function () {
      const rows = document.querySelectorAll('.delivery-item-row');
      const totalQtySpan = document.getElementById('total-received-qty');
      const totalKgSpan = document.getElementById('total-received-kg');

      function checkDiscrepanciesAndRecalculate() {
        let totalQty = 0;
        let totalKg = 0;

        rows.forEach(row => {
          const idx = row.dataset.index;
          const reqQty = parseFloat(row.dataset.reqQty) || 0;
          const reqKg = parseFloat(row.dataset.reqKg) || 0;

          const qtyInput = row.querySelector('.input-delivered-qty');
          const kgInput = row.querySelector('.input-delivered-kg');

          const delQty = parseFloat(qtyInput.value) || 0;
          const delKg = parseFloat(kgInput.value) || 0;

          totalQty += delQty;
          totalKg += delKg;

          // Check Qty difference
          const qtyDiffLabel = document.getElementById(`qty-diff-${idx}`);
          if (Math.abs(delQty - reqQty) > 0.0001) {
            qtyInput.classList.add('discrepancy-warning');
            qtyDiffLabel.classList.add('show');
          } else {
            qtyInput.classList.remove('discrepancy-warning');
            qtyDiffLabel.classList.remove('show');
          }

          // Check Kg difference
          const kgDiffLabel = document.getElementById(`kg-diff-${idx}`);
          if (Math.abs(delKg - reqKg) > 0.0001) {
            kgInput.classList.add('discrepancy-warning');
            kgDiffLabel.classList.add('show');
          } else {
            kgInput.classList.remove('discrepancy-warning');
            kgDiffLabel.classList.remove('show');
          }
        });

        totalQtySpan.textContent = totalQty.toFixed(3);
        totalKgSpan.textContent = totalKg.toFixed(3);
      }

      // Add event listeners for live calculations
      rows.forEach(row => {
        const qtyInput = row.querySelector('.input-delivered-qty');
        const kgInput = row.querySelector('.input-delivered-kg');

        qtyInput.addEventListener('input', checkDiscrepanciesAndRecalculate);
        kgInput.addEventListener('input', checkDiscrepanciesAndRecalculate);
      });

      // Initial check
      checkDiscrepanciesAndRecalculate();
    });
  </script>

@endsection
