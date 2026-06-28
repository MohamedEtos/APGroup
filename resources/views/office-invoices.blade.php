@extends('layout.app')

@section('content')

@php
  $typeBadges = [
      'طباعة'   => 'bg-gradient-primary',
      'ليزر'    => 'bg-gradient-info',
      'رول بريس'=> 'bg-gradient-warning',
      'ستراس'   => 'bg-gradient-success',
  ];
@endphp

<div class="container-fluid py-4">

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
      <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
      <span class="alert-text"><strong>نجاح!</strong> {{ session('success') }}</span>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

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

  <style>
    .invoice-builder-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .invoice-items-table input,
    .invoice-items-table select {
        border: 1px solid #d2d6da;
        box-shadow: none;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease-in-out;
        font-size: 0.875rem;
    }
    
    .invoice-items-table input:focus,
    .invoice-items-table select:focus {
        border-color: #cb0c9f;
        box-shadow: 0 0 0 2px rgba(203, 12, 159, 0.15);
        background-color: #fff;
        outline: none;
    }
    
    .invoice-items-table th {
        font-size: 0.75rem !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8392ab !important;
    }
    
    .invoice-summary-box {
        background-color: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    
    .add-row-btn {
        border: 2px dashed #cb0c9f;
        color: #cb0c9f !important;
        transition: all 0.2s ease-in-out;
        background: transparent;
    }
    
    .add-row-btn:hover {
        background-color: rgba(203, 12, 159, 0.05);
        border-color: #cb0c9f;
    }
  </style>

  <div class="row">
    {{-- Full-width Invoice Builder Column --}}
    <div class="col-12 mb-4">
      <div class="card invoice-builder-card">
        <div class="card-header pb-0">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">إنشاء فاتورة مكتب جديدة</h6>
              <p class="text-xs text-secondary mb-0">أدخل تفاصيل الفاتورة وأضف الأصناف لحساب الإجماليات تلقائياً</p>
            </div>
            <div class="logo-container align-self-start">
              <span class="badge bg-gradient-primary font-weight-bold">ApGroup المكتب الرئيسي</span>
            </div>
          </div>
        </div>
        <div class="card-body mt-2">
          <form action="{{ route('office-invoices.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Invoice Header metadata --}}
            <div class="row mb-4">
              <div class="col-md-4 col-12 mb-3">
                <label for="invoice" class="form-label text-xs font-weight-bold text-secondary">رقم الفاتورة</label>
                <input type="text" class="form-control form-control-sm @error('invoice') is-invalid @enderror" id="invoice" name="invoice" value="{{ old('invoice') }}" placeholder="مثال: INV-1001" required>
              </div>

              <div class="col-md-4 col-12 mb-3">
                <label for="receiver" class="form-label text-xs font-weight-bold text-secondary">اسم العميل (المستلم)</label>
                <input type="text" class="form-control form-control-sm @error('receiver') is-invalid @enderror" id="receiver" name="receiver" value="{{ old('receiver') }}" placeholder="اسم العميل المستلم للفاتورة" required>
              </div>

              <div class="col-md-4 col-12 mb-3">
                <label for="img" class="form-label text-xs font-weight-bold text-secondary">صورة الفاتورة (اختياري)</label>
                <input type="file" class="form-control form-control-sm @error('img') is-invalid @enderror" id="img" name="img" accept="image/*">
              </div>
            </div>

            {{-- Invoice Items Table --}}
            <div class="table-responsive">
              <table class="table align-items-center mb-0 invoice-items-table">
                <thead>
                  <tr>
                    <th class="text-center font-weight-bolder" style="width: 5%;">#</th>
                    <th class="font-weight-bolder">كود التوب</th>
                    <th class="font-weight-bolder" style="width: 25%;">النوع</th>
                    <th class="text-center font-weight-bolder" style="width: 15%;">الكمية</th>
                    <th class="text-center font-weight-bolder" style="width: 15%;">سعر الوحدة</th>
                    <th class="text-center font-weight-bolder" style="width: 15%;">الإجمالي</th>
                    <th class="text-center font-weight-bolder" style="width: 5%;">إجراء</th>
                  </tr>
                </thead>
                <tbody id="invoice-items-body">
                  {{-- Rows will be injected dynamically --}}
                </tbody>
              </table>
            </div>

            {{-- Add Item Button --}}
            <div class="mt-3">
              <button type="button" class="btn btn-sm w-100 add-row-btn py-2 font-weight-bold" id="add-item-row-btn">
                <i class="fas fa-plus me-2"></i> إضافة صنف جديد
              </button>
            </div>

            {{-- Totals Summary & Submit Button --}}
            <div class="row mt-4 align-items-center">
              <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                <div class="invoice-summary-box p-3 d-flex justify-content-around text-center">
                  <div>
                    <span class="text-xs text-secondary font-weight-bold d-block">إجمالي الكميات</span>
                    <span class="text-lg font-weight-bold text-dark" id="total-qty">0</span>
                  </div>
                  <div class="vr bg-secondary opacity-25"></div>
                  <div>
                    <span class="text-xs text-secondary font-weight-bold d-block">الإجمالي الكلي</span>
                    <span class="text-lg font-weight-bold text-success"><span id="grand-total">0.00</span> ج.م</span>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-12 d-flex justify-content-lg-end justify-content-center">
                <button type="submit" class="btn btn-md bg-gradient-primary mb-0 py-2.5 px-5 font-weight-bold">
                  <i class="fas fa-save me-2"></i> حفظ وتسجيل الفاتورة
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Invoices Table Column (Last 5) --}}
    <div class="col-12 mb-4">
      <div class="card h-100">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">آخر 5 فواتير مضافة</h6>
            <p class="text-xs text-secondary mb-0">جدول يعرض آخر 5 فواتير تم تسجيلها حديثاً مع الإجماليات</p>
          </div>
          <a href="{{ route('tables') }}" class="btn btn-sm btn-outline-primary mb-0">عرض كل الفواتير</a>
        </div>
        
        <div class="card-body px-0 pt-0 pb-2 mt-3">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="font-weight-bolder text-end pe-3">#id</th>
                  <th class="font-weight-bolder text-end pe-3">رقم الفاتورة</th>
                  <th class="font-weight-bolder text-end pe-3">اسم العميل</th>
                  <th class="font-weight-bolder text-center">الكمية</th>
                  <th class="font-weight-bolder text-center">السعر (متوسط)</th>
                  <th class="font-weight-bolder text-center">الإجمالي</th>
                  <th class="font-weight-bolder text-center">النوع</th>
                  <th class="font-weight-bolder text-center">التاريخ</th>
                  <th class="font-weight-bolder text-center">الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                @forelse($invoices as $invoice)
                  <tr>
                    <td class="text-end pe-3">
                      <span class="text-xs font-weight-bold">{{ $invoice->id }}</span>
                    </td>
                    <td class="text-end pe-3">
                      <span class="text-xs font-weight-bold text-primary">{{ $invoice->invoice }}</span>
                    </td>
                    <td class="text-end pe-3">
                      <span class="text-xs font-weight-bold">{{ $invoice->receiver }}</span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm bg-gradient-secondary">{{ $invoice->qty }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-xs font-weight-bold">{{ number_format($invoice->price, 2) }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-xs font-weight-bold text-success">
                        @php
                          $subtotal = 0;
                          if ($invoice->items && is_array($invoice->items)) {
                              foreach ($invoice->items as $item) {
                                  $subtotal += ($item['qty'] ?? 0) * ($item['price'] ?? 0);
                              }
                          } else {
                              $subtotal = $invoice->qty * $invoice->price;
                          }
                        @endphp
                        {{ number_format($subtotal, 2) }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge badge-sm {{ $typeBadges[$invoice->type] ?? 'bg-gradient-dark' }}">{{ $invoice->type }}</span>
                    </td>
                    <td class="text-center">
                      <span class="text-xs text-secondary font-weight-bold">{{ $invoice->date }}</span>
                    </td>
                    <td class="text-center">
                      <a href="{{ route('invoice-receipt.show', $invoice->id) }}" class="btn btn-sm btn-outline-info mb-0 py-1 px-2" title="عرض الفاتورة الكتابية" target="_blank">
                        <i class="fas fa-print fa-xs"></i> عرض / طباعة
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center py-4 text-secondary">
                      لا توجد فواتير مضافة حالياً.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsBody = document.getElementById('invoice-items-body');
        const addItemBtn = document.getElementById('add-item-row-btn');
        const totalQtySpan = document.getElementById('total-qty');
        const grandTotalSpan = document.getElementById('grand-total');
        let rowCounter = 0;
    
        // Type options array
        const typeOptions = ['طباعة', 'ليزر', 'رول بريس', 'ستراس'];
    
        // Function to add a new row
        function addRow(code = '', type = '', qty = 1, price = 0) {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.dataset.index = rowCounter;
    
            let optionsHtml = `<option value="" disabled ${type === '' ? 'selected' : ''}>اختر النوع</option>`;
            typeOptions.forEach(opt => {
                optionsHtml += `<option value="${opt}" ${type === opt ? 'selected' : ''}>${opt}</option>`;
            });
    
            tr.innerHTML = `
                <td class="text-center align-middle row-number font-weight-bold text-secondary text-xs"></td>
                <td>
                    <input type="text" name="items[${rowCounter}][code]" class="form-control form-control-sm item-code" value="${code}" placeholder="مثال: TOP-A1" required>
                </td>
                <td>
                    <select class="form-select form-select-sm item-type" name="items[${rowCounter}][type]" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${rowCounter}][qty]" class="form-control form-control-sm text-center item-qty" value="${qty}" min="1" required>
                </td>
                <td>
                    <input type="number" name="items[${rowCounter}][price]" class="form-control form-control-sm text-center item-price" value="${price}" min="0" step="0.01" required>
                </td>
                <td class="text-center align-middle">
                    <span class="text-sm font-weight-bold text-dark item-total">0.00</span> ج.م
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0 remove-row-btn">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            `;
    
            itemsBody.appendChild(tr);
            rowCounter++;
    
            // Attach event listeners
            const qtyInput = tr.querySelector('.item-qty');
            const priceInput = tr.querySelector('.item-price');
            const removeBtn = tr.querySelector('.remove-row-btn');
    
            qtyInput.addEventListener('input', calculateTotals);
            priceInput.addEventListener('input', calculateTotals);
            removeBtn.addEventListener('click', function() {
                tr.remove();
                renumberRows();
                calculateTotals();
            });
    
            renumberRows();
            calculateTotals();
        }
    
        // Function to renumber the row display indices
        function renumberRows() {
            const rows = itemsBody.querySelectorAll('.item-row');
            rows.forEach((row, idx) => {
                row.querySelector('.row-number').textContent = idx + 1;
            });
        }
    
        // Function to calculate all totals
        function calculateTotals() {
            const rows = itemsBody.querySelectorAll('.item-row');
            let totalQty = 0;
            let grandTotal = 0;
    
            rows.forEach(row => {
                const qty = parseInt(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const total = qty * price;
    
                row.querySelector('.item-total').textContent = total.toFixed(2);
    
                totalQty += qty;
                grandTotal += total;
            });
    
            totalQtySpan.textContent = totalQty;
            grandTotalSpan.textContent = grandTotal.toFixed(2);
        }
    
        // Add first row on load if there's no old inputs
        @if(old('items') && is_array(old('items')))
            @foreach(old('items') as $idx => $oldItem)
                addRow("{{ $oldItem['code'] ?? '' }}", "{{ $oldItem['type'] ?? '' }}", {{ $oldItem['qty'] ?? 1 }}, {{ $oldItem['price'] ?? 0 }});
            @endforeach
        @else
            addRow();
        @endif
    
        // Add row on click
        addItemBtn.addEventListener('click', function() {
            addRow();
        });
    
        // Form submit validation to make sure there's at least one row
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const rows = itemsBody.querySelectorAll('.item-row');
            if (rows.length === 0) {
                e.preventDefault();
                alert('يجب إضافة صنف واحد على الأقل في الفاتورة.');
            }
        });
    });
  </script>

  <footer class="footer pt-3">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div class="copyright text-center text-sm text-muted text-lg-end">
            © <script>document.write(new Date().getFullYear())</script>,
            made with <i class="fa fa-heart"></i> by
            <a href="https://github.com/MohamedMahrous1" class="font-weight-bold" target="_blank">Mohamed Mahrous</a>
            for a better web.
          </div>
        </div>
      </div>
    </div>
  </footer>
</div>

@endsection
