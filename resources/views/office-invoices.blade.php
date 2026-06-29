@extends('layout.app')

@section('content')

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
        border: 1px solid rgba(0, 0, 0, 0.05);
      }

      .invoice-items-table input,
      .invoice-items-table select {
        border: 1px solid #d2d6da;
        box-shadow: none;
        border-radius: 8px;
        padding: 0.45rem 0.65rem;
        transition: all 0.2s ease-in-out;
        font-size: 0.85rem;
      }

      .invoice-items-table input:focus,
      .invoice-items-table select:focus {
        border-color: #cb0c9f;
        box-shadow: 0 0 0 2px rgba(203, 12, 159, 0.15);
        background-color: #fff;
        outline: none;
      }

      .invoice-items-table th {
        font-size: 0.72rem !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8392ab !important;
        white-space: nowrap;
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

      /* Color swatch preview */
      .color-preview {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #dee2e6;
        display: inline-block;
        vertical-align: middle;
        margin-right: 4px;
        cursor: pointer;
        flex-shrink: 0;
      }

      .color-input-wrapper {
        display: flex;
        align-items: center;
        gap: 4px;
      }

      .unit-toggle {
        display: flex;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #d2d6da;
      }

      .unit-toggle input[type="radio"] {
        display: none;
      }

      .unit-toggle label {
        flex: 1;
        text-align: center;
        padding: 0.18rem 0.3rem;
        font-size: 0.68rem;
        cursor: pointer;
        background: #fff;
        color: #8392ab;
        transition: all 0.2s ease;
        border: none;
        margin: 0;
        border-radius: 0;
        line-height: 1.3;
      }

      .unit-toggle input[type="radio"]:checked+label {
        background: #cb0c9f;
        color: #fff;
        font-weight: 700;
      }

      .unit-toggle label:not(:last-child) {
        border-left: 1px solid #d2d6da;
      }

      /* Collapsible invoice items row */
      .invoice-items-collapse {
        display: none;
        transition: all 0.3s ease;
      }
      .invoice-items-collapse.show {
        display: table-row;
      }
      tr.main-invoice-row {
        cursor: pointer;
        user-select: none;
      }
      tr.main-invoice-row:hover td {
        background-color: rgba(203, 12, 159, 0.04);
      }
      .toggle-arrow {
        display: inline-block;
        transition: transform 0.25s ease;
        font-size: 0.7rem;
        color: #8392ab;
        margin-right: 4px;
      }
      .toggle-arrow.open {
        transform: rotate(90deg);
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
                <span class="font-weight-bold">ApGroup المكتب الرئيسي</span>
              </div>
            </div>
          </div>
          <div class="card-body mt-2">
            <form action="{{ route('office-invoices.store') }}" method="POST" enctype="multipart/form-data">
              @csrf

              {{-- Invoice Header metadata --}}
              <div class="row mb-4">
                <div class="col-md-4 col-12 mb-3">
                  <label for="invoice_number" class="form-label text-xs font-weight-bold text-secondary">رقم
                    الفاتورة</label>
                  <input type="text" class="form-control form-control-sm @error('invoice_number') is-invalid @enderror"
                    id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}"
                    placeholder="مثال: INV-1001" required>
                </div>

                <div class="col-md-4 col-12 mb-3">
                  <label for="receiver" class="form-label text-xs font-weight-bold text-secondary">اسم العميل
                    (المستلم)</label>
                  <input type="text" class="form-control form-control-sm @error('receiver') is-invalid @enderror"
                    id="receiver" name="receiver" value="{{ old('receiver') }}" placeholder="اسم العميل المستلم للفاتورة"
                    required>
                </div>

                <div class="col-md-4 col-12 mb-3">
                  <label for="img" class="form-label text-xs font-weight-bold text-secondary">صورة الفاتورة
                    (اختياري)</label>
                  <input type="file" class="form-control form-control-sm @error('img') is-invalid @enderror" id="img"
                    name="img" accept="image/*">
                </div>
              </div>

              {{-- Invoice Items Table --}}
              <div class="table-responsive">
                <table class="table align-items-center mb-0 invoice-items-table">
                  <thead>
                    <tr>
                      <th class="text-center font-weight-bolder" style="width: 4%;">#</th>
                      <th class="font-weight-bolder" style="min-width: 110px;">كود التوب</th>
                      <th class="font-weight-bolder" style="min-width: 120px;">النوع</th>
                      <th class="font-weight-bolder" style="min-width: 130px;">لون القماش</th>
                      <th class="text-center font-weight-bolder" style="min-width: 180px;">الكمية والوحدة</th>
                      <th class="text-center font-weight-bolder" style="min-width: 100px;">سعر الوحدة</th>
                      <th class="text-center font-weight-bolder" style="min-width: 90px;">الإجمالي</th>
                      <th class="text-center font-weight-bolder" style="width: 5%;">حذف</th>
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
              <p class="text-xs text-secondary mb-0">جدول يعرض آخر 5 فواتير تم تسجيلها حديثاً</p>
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
                    <th class="font-weight-bolder text-center">إجمالي الكمية</th>
                    <th class="font-weight-bolder text-center">الإجمالي الكلي</th>
                    <th class="font-weight-bolder text-center">التاريخ</th>
                    <th class="font-weight-bolder text-center">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($invoices as $invoice)
                    {{-- الصف الرئيسي قابل للنقر --}}
                    <tr class="main-invoice-row" data-target="items-{{ $invoice->id }}" title="اضغط لعرض التفاصيل">
                      <td class="text-end pe-3">
                        <i class="fas fa-chevron-left toggle-arrow" id="arrow-{{ $invoice->id }}"></i>
                        <span class="text-xs font-weight-bold">{{ $invoice->id }}</span>
                      </td>
                      <td class="text-end pe-3">
                        <span class="text-xs font-weight-bold text-primary">{{ $invoice->invoice_number }}</span>
                      </td>
                      <td class="text-end pe-3">
                        <span class="text-xs font-weight-bold">{{ $invoice->receiver }}</span>
                      </td>
                      <td class="text-center">
                        <span class="text-xs font-weight-bold">{{ number_format($invoice->total_qty, 3) }}</span>
                      </td>
                      <td class="text-center">
                        <span class="text-xs font-weight-bold text-success">{{ number_format($invoice->total, 2) }} ج.م</span>
                      </td>
                      <td class="text-center">
                        <span class="text-xs text-secondary font-weight-bold">{{ $invoice->date }}</span>
                      </td>
                      <td class="text-center" onclick="event.stopPropagation()">
                        <a href="{{ route('invoice-receipt.show', $invoice->id) }}"
                          class="btn btn-sm btn-outline-info mb-0 py-1 px-2" title="عرض الفاتورة الكتابية" target="_blank">
                          <i class="fas fa-print fa-xs"></i> عرض / طباعة
                        </a>
                      </td>
                    </tr>

                    {{-- محتويات الفاتورة (مخفية بالأساس) --}}
                    @if($invoice->items->count())
                      <tr class="invoice-items-collapse bg-light" id="items-{{ $invoice->id }}">
                        <td colspan="7" class="p-0">
                          <div class="px-4 py-3">
                            <small class="text-secondary font-weight-bold text-xs">
                              <i class="fas fa-list-ul me-1"></i> محتويات الفاتورة ({{ $invoice->items->count() }} صنف)
                            </small>
                            <table class="table table-sm mb-0 mt-2">
                              <thead>
                                <tr>
                                  <th class="text-xs text-secondary">#</th>
                                  <th class="text-xs text-secondary">الكود</th>
                                  <th class="text-xs text-secondary">النوع</th>
                                  <th class="text-xs text-secondary">لون القماش</th>
                                  <th class="text-xs text-secondary text-center">الكمية</th>
                                  <th class="text-xs text-secondary text-center">الوحدة</th>
                                  <th class="text-xs text-secondary text-center">السعر</th>
                                  <th class="text-xs text-secondary text-center">الإجمالي</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($invoice->items as $i => $item)
                                  <tr>
                                    <td class="text-xs">{{ $i + 1 }}</td>
                                    <td class="text-xs font-weight-bold">{{ $item->code }}</td>
                                    <td class="text-xs">{{ $item->type }}</td>
                                    <td class="text-xs">
                                      @if($item->fabric_color)
                                        <span style="display:inline-block; width:14px; height:14px; border-radius:50%; background:{{ $item->fabric_color }}; border:1px solid #ccc; vertical-align:middle; margin-left:4px;"></span>
                                        {{ $item->fabric_color }}
                                      @else
                                        <span class="text-muted">—</span>
                                      @endif
                                    </td>
                                    <td class="text-xs text-center">{{ number_format($item->qty, 3) }}</td>
                                    <td class="text-xs text-center"><span class="">{{ $item->unit }}</span></td>
                                    <td class="text-xs text-center">{{ number_format($item->price, 2) }} ج.م</td>
                                    <td class="text-xs text-center font-weight-bold">{{ number_format($item->subtotal, 2) }} ج.م</td>
                                  </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </td>
                      </tr>
                    @endif

                  @empty
                    <tr>
                      <td colspan="7" class="text-center py-4 text-secondary">
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
      document.addEventListener('DOMContentLoaded', function () {

        // ── Collapsible invoice rows ──────────────────────────────────────
        document.querySelectorAll('tr.main-invoice-row').forEach(function(row) {
          row.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const collapseRow = document.getElementById(targetId);
            if (!collapseRow) return;

            const arrowId = 'arrow-' + targetId.replace('items-', '');
            const arrow   = document.getElementById(arrowId);

            if (collapseRow.classList.contains('show')) {
              collapseRow.classList.remove('show');
              if (arrow) arrow.classList.remove('open');
            } else {
              collapseRow.classList.add('show');
              if (arrow) arrow.classList.add('open');
            }
          });
        });
        // ─────────────────────────────────────────────────────────────────

        const itemsBody = document.getElementById('invoice-items-body');
        const addItemBtn = document.getElementById('add-item-row-btn');
        const totalQtySpan = document.getElementById('total-qty');
        const grandTotalSpan = document.getElementById('grand-total');
        let rowCounter = 0;

        // Function to add a new row
        function addRow(data = {}) {
          const idx = rowCounter;
          const code = data.code || '';
          const type = data.type || '';
          const fabricColor = data.fabric_color || '';
          const qty = data.qty !== undefined ? data.qty : '';
          const unit = data.unit || 'كيلو';
          const price = data.price !== undefined ? data.price : '';

          const units = ['كيلو', 'متر', 'قطعة'];
          let unitRadios = '';
          units.forEach(u => {
            const checked = (u === unit) ? 'checked' : '';
            unitRadios += `
                      <input type="radio" name="items[${idx}][unit]" id="unit_${idx}_${u}" value="${u}" ${checked} required>
                      <label for="unit_${idx}_${u}">${u}</label>
                  `;
          });

          const tr = document.createElement('tr');
          tr.className = 'item-row';
          tr.dataset.index = idx;

          tr.innerHTML = `
                  <td class="text-center align-middle row-number font-weight-bold text-secondary text-xs"></td>
                  <td>
                      <input type="text" name="items[${idx}][code]"
                             class="form-control form-control-sm item-code"
                             value="${escHtml(code)}" placeholder="مثال: TOP-A1" required>
                  </td>
                  <td>
                      <input type="text" name="items[${idx}][type]"
                             class="form-control form-control-sm item-type"
                             value="${escHtml(type)}" placeholder="مثال: طباعة" required>
                  </td>
                  <td>
                      <div class="color-input-wrapper">
                          <input type="color" class="color-preview item-color-picker" value="${fabricColor || '#ffffff'}" title="اختر لون القماش">
                          <input type="text" name="items[${idx}][fabric_color]"
                                 class="form-control form-control-sm item-fabric-color"
                                 value="${escHtml(fabricColor)}" placeholder="#FFFFFF أو اسم اللون">
                      </div>
                  </td>
                  <td>
                      <div style="display:flex; flex-direction:column; gap:6px;">
                          <input type="number" name="items[${idx}][qty]"
                                 class="form-control form-control-sm text-center item-qty"
                                 value="${qty}" min="0.001" step="0.001" placeholder="0.000" required>
                          <div class="unit-toggle">
                              ${unitRadios}
                          </div>
                      </div>
                  </td>
                  <td>
                      <input type="number" name="items[${idx}][price]"
                             class="form-control form-control-sm text-center item-price"
                             value="${price}" min="0" step="0.01" placeholder="0.00" required>
                  </td>
                  <td class="text-center align-middle">
                      <span class="text-sm font-weight-bold text-dark item-total">0.00</span> ج.م
                  </td>
                  <td class="text-center align-middle">
                      <button type="button" class="btn btn-link text-danger  px-3 mb-0 remove-row-btn">
                          <i class="far fa-trash-alt"></i>
                      </button>
                  </td>
              `;

          itemsBody.appendChild(tr);
          rowCounter++;

          // Sync color picker → text input
          const colorPicker = tr.querySelector('.item-color-picker');
          const colorText = tr.querySelector('.item-fabric-color');

          colorPicker.addEventListener('input', function () {
            colorText.value = this.value;
          });
          colorText.addEventListener('input', function () {
            // Try to set picker value if it's a valid hex color
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
              colorPicker.value = this.value;
            }
          });

          const qtyInput = tr.querySelector('.item-qty');
          const priceInput = tr.querySelector('.item-price');
          const removeBtn = tr.querySelector('.remove-row-btn');

          qtyInput.addEventListener('input', calculateTotals);
          priceInput.addEventListener('input', calculateTotals);
          removeBtn.addEventListener('click', function () {
            tr.remove();
            renumberRows();
            calculateTotals();
          });

          renumberRows();
          calculateTotals();
        }

        // Escape HTML helper
        function escHtml(str) {
          return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
        }

        // Renumber rows
        function renumberRows() {
          const rows = itemsBody.querySelectorAll('.item-row');
          rows.forEach((row, idx) => {
            row.querySelector('.row-number').textContent = idx + 1;
          });
        }

        // Calculate totals
        function calculateTotals() {
          const rows = itemsBody.querySelectorAll('.item-row');
          let totalQty = 0;
          let grandTotal = 0;

          rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = qty * price;

            row.querySelector('.item-total').textContent = total.toFixed(2);
            totalQty += qty;
            grandTotal += total;
          });

          totalQtySpan.textContent = totalQty.toFixed(3);
          grandTotalSpan.textContent = grandTotal.toFixed(2);
        }

        // Restore old input on validation failure
        @if(old('items') && is_array(old('items')))
          @foreach(old('items') as $idx => $oldItem)
            addRow({
              code: "{{ addslashes($oldItem['code'] ?? '') }}",
              type: "{{ addslashes($oldItem['type'] ?? '') }}",
              fabric_color: "{{ addslashes($oldItem['fabric_color'] ?? '') }}",
              qty:          {{ $oldItem['qty'] ?? '' }},
              unit: "{{ $oldItem['unit'] ?? 'قطعة' }}",
              price:        {{ $oldItem['price'] ?? 0 }},
            });
          @endforeach
        @else
          addRow();
        @endif

        // Add row on click
        addItemBtn.addEventListener('click', function () {
          addRow();
        });

        // Form submit validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
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
              ©
              <script>document.write(new Date().getFullYear())</script>,
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