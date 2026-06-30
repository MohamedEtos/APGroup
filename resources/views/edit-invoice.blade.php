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
    </style>

    <div class="row">
      {{-- Full-width Invoice Builder Column --}}
      <div class="col-12 mb-4">
        <div class="card invoice-builder-card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0">تعديل فاتورة مكتب رقم: {{ $invoice->invoice_number }}</h6>
                <p class="text-xs text-secondary mb-0">قم بتحديث تفاصيل الفاتورة والأصناف ثم اضغط حفظ</p>
              </div>
              <div class="logo-container align-self-start">
                <span class="font-weight-bold">ApGroup المكتب الرئيسي</span>
              </div>
            </div>
          </div>
          <div class="card-body mt-2">
            <form action="{{ route('office-invoices.update', $invoice->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              {{-- Invoice Header metadata --}}
              <div class="row mb-4">
                <div class="col-md-4 col-12 mb-3">
                  <label for="invoice_number" class="form-label text-xs font-weight-bold text-secondary">رقم الفاتورة</label>
                  <input type="text" class="form-control form-control-sm @error('invoice_number') is-invalid @enderror"
                    id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}"
                    placeholder="مثال: INV-1001" required>
                </div>

                <div class="col-md-4 col-12 mb-3">
                  <label for="receiver" class="form-label text-xs font-weight-bold text-secondary">اسم العميل (المستلم)</label>
                  <input type="text" class="form-control form-control-sm @error('receiver') is-invalid @enderror"
                    id="receiver" name="receiver" value="{{ old('receiver', $invoice->receiver) }}" placeholder="اسم العميل المستلم للفاتورة"
                    required>
                </div>

                <div class="col-md-4 col-12 mb-3">
                  <label class="form-label text-xs font-weight-bold text-secondary">صورة الفاتورة (اختياري)</label>

                  {{-- Hidden inputs --}}
                  <input type="hidden" name="img_base64" id="img_base64">
                  <input type="hidden" name="img_removed" id="img_removed" value="0">

                  {{-- Visible file picker --}}
                  <input type="file" class="form-control form-control-sm @error('img') is-invalid @enderror"
                    id="imgPicker" accept="image/*" onchange="handleImageSelect(this)">

                  {{-- Quality slider --}}
                  <div id="qualityRow" class="mt-2 d-none">
                    <div class="d-flex align-items-center gap-2">
                      <label class="text-xs text-secondary mb-0 text-nowrap">جودة:</label>
                      <input type="range" id="qualitySlider" min="10" max="95" value="72"
                             class="form-range" style="flex:1;" oninput="updateQualityLabel();recompress()">
                      <span id="qualityLabel" class="text-xs font-weight-bold" style="width:32px;">72%</span>
                    </div>
                  </div>

                  {{-- Preview + stats --}}
                  @php
                    $hasImg = $invoice->img && $invoice->img !== 'assets/img/team-2.jpg';
                  @endphp
                  <div id="imgPreviewBox" class="mt-2 {{ $hasImg ? '' : 'd-none' }}">
                    <div class="d-flex gap-3 align-items-start">
                      <img id="imgPreview" src="{{ $hasImg ? asset($invoice->img) : '' }}" alt="معاينة"
                           style="width:72px;height:72px;object-fit:cover;border-radius:8px;
                                  border:2px solid #e9ecef;box-shadow:0 2px 6px rgba(0,0,0,.1);cursor:zoom-in;"
                           onclick="openPreviewModal()">
                      <div style="font-size:0.75rem;line-height:1.7;">
                        <div id="originalSizeWrapper" class="{{ $hasImg ? 'd-none' : '' }}">
                          <span class="text-secondary">الأصلي:</span> <strong id="sizeOriginal">—</strong>
                        </div>
                        <div id="compressedSizeWrapper" class="{{ $hasImg ? 'd-none' : '' }}">
                          <span class="text-secondary">بعد الضغط:</span> <strong id="sizeCompressed" class="text-success">—</strong>
                        </div>
                        <div id="savedSizeWrapper" class="{{ $hasImg ? 'd-none' : '' }}">
                          <span class="text-secondary">التوفير:</span> <strong id="sizeSaved" class="text-primary">—</strong>
                        </div>
                        @if($hasImg)
                          <div id="currentImgLabel" class="text-primary font-weight-bold">الصورة الحالية الفعالة</div>
                        @endif
                        <div class="text-muted" style="font-size:0.68rem;">WebP · أسرع تحميل</div>
                      </div>
                    </div>
                    <button type="button" class="btn btn-link text-danger p-0 mt-1 text-xs" onclick="removeImage()">
                      <i class="far fa-trash-alt me-1"></i> إزالة الصورة
                    </button>
                  </div>
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
                      <th class="text-center font-weight-bolder" style="min-width: 180px;">العدد والوحدة</th>
                      <th class="text-center font-weight-bolder" style="min-width: 120px;">الإجمالي </th>
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

              {{-- Summary & Submit Button --}}
              <div class="row mt-4 align-items-center">
                <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                  <div class="invoice-summary-box p-3 d-flex justify-content-around text-center">
                    <div>
                      <span class="text-xs text-secondary font-weight-bold d-block">إجمالي العدد</span>
                      <span class="text-lg font-weight-bold text-dark" id="total-qty">0</span>
                    </div>
                    <div class="vr bg-secondary opacity-25"></div>
                    <div>
                      <span class="text-xs text-secondary font-weight-bold d-block">إجمالي الكيلو</span>
                      <span class="text-lg font-weight-bold text-primary"><span id="grand-total-kg">0.000</span> كيلو</span>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 col-12 d-flex justify-content-lg-end justify-content-center gap-2">
                  <a href="{{ route('tables') }}" class="btn btn-md btn-outline-secondary mb-0 py-2.5 px-4 font-weight-bold">
                    إلغاء
                  </a>
                  <button type="submit" class="btn btn-md bg-gradient-primary mb-0 py-2.5 px-5 font-weight-bold">
                    <i class="fas fa-save me-2"></i> حفظ التعديلات
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {

        const itemsBody = document.getElementById('invoice-items-body');
        const addItemBtn = document.getElementById('add-item-row-btn');
        const totalQtySpan = document.getElementById('total-qty');
        const grandTotalKgSpan = document.getElementById('grand-total-kg');
        let rowCounter = 0;

        // Function to add a new row
        function addRow(data = {}) {
          const idx = rowCounter;
          const code = data.code || '';
          const type = data.type || '';
          const fabricColor = data.fabric_color || '';
          const qty = data.qty !== undefined ? data.qty : '';
          const unit = data.unit || 'توب';
          const totalKg = data.total_kg !== undefined ? data.total_kg : '';

          const units = ['توب', 'متر', 'كيلو'];
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
                      <input type="number" name="items[${idx}][total_kg]"
                             class="form-control form-control-sm text-center item-total-kg"
                             value="${totalKg}" min="0" step="0.001" placeholder="0.000" required>
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
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
              colorPicker.value = this.value;
            }
          });

          const qtyInput = tr.querySelector('.item-qty');
          const totalKgInput = tr.querySelector('.item-total-kg');
          const removeBtn = tr.querySelector('.remove-row-btn');

          qtyInput.addEventListener('input', calculateTotals);
          totalKgInput.addEventListener('input', calculateTotals);
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
          let grandTotalKg = 0;

          rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const kg = parseFloat(row.querySelector('.item-total-kg').value) || 0;

            totalQty += qty;
            grandTotalKg += kg;
          });

          totalQtySpan.textContent = totalQty.toFixed(3);
          grandTotalKgSpan.textContent = grandTotalKg.toFixed(3);
        }

        // Restore old input on validation failure or load existing items
        @if(old('items') && is_array(old('items')))
          @foreach(old('items') as $idx => $oldItem)
            addRow({
              code: "{{ addslashes($oldItem['code'] ?? '') }}",
              type: "{{ addslashes($oldItem['type'] ?? '') }}",
              fabric_color: "{{ addslashes($oldItem['fabric_color'] ?? '') }}",
              qty:      parseFloat("{{ $oldItem['qty'] ?? 0 }}") || 0,
              unit: "{{ $oldItem['unit'] ?? 'كيلو' }}",
              total_kg: parseFloat("{{ $oldItem['total_kg'] ?? 0 }}") || 0,
            });
          @endforeach
        @else
          @foreach($invoice->items as $item)
            addRow({
              code: "{{ addslashes($item->code ?? '') }}",
              type: "{{ addslashes($item->type ?? '') }}",
              fabric_color: "{{ addslashes($item->fabric_color ?? '') }}",
              qty:      parseFloat("{{ $item->qty ?? 0 }}") || 0,
              unit: "{{ $item->unit ?? 'كيلو' }}",
              total_kg: parseFloat("{{ $item->total_kg ?? 0 }}") || 0,
            });
          @endforeach
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

    {{-- ===== Image Compression Script ===== --}}
    <div id="previewModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.82);align-items:center;justify-content:center;" onclick="closePreviewModal()">
      <div style="position:relative;" onclick="event.stopPropagation()">
        <button onclick="closePreviewModal()" style="position:absolute;top:-13px;right:-13px;width:30px;height:30px;border-radius:50%;background:#fff;border:none;font-size:17px;line-height:1;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.3);">&times;</button>
        <img id="previewModalImg" src="" style="max-width:90vw;max-height:88vh;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.5);display:block;">
      </div>
    </div>

    <script>
      let originalFile = null;
      let compressedBlob = null;

      function openPreviewModal() {
        let src = '';
        if (compressedBlob) {
          src = URL.createObjectURL(compressedBlob);
        } else {
          src = document.getElementById('imgPreview').src;
        }
        if (!src) return;
        document.getElementById('previewModalImg').src = src;
        document.getElementById('previewModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
      function closePreviewModal() {
        document.getElementById('previewModal').style.display = 'none';
        document.body.style.overflow = '';
      }
      document.addEventListener('keydown', e => { if (e.key === 'Escape') closePreviewModal(); });

      function handleImageSelect(input) {
        const file = input.files[0];
        if (!file) return;
        originalFile = file;
        document.getElementById('img_removed').value = '0';
        document.getElementById('qualityRow').classList.remove('d-none');
        document.getElementById('imgPreviewBox').classList.remove('d-none');

        document.getElementById('originalSizeWrapper').classList.remove('d-none');
        document.getElementById('compressedSizeWrapper').classList.remove('d-none');
        document.getElementById('savedSizeWrapper').classList.remove('d-none');

        const currentImgLabel = document.getElementById('currentImgLabel');
        if (currentImgLabel) currentImgLabel.remove();

        document.getElementById('sizeOriginal').textContent = formatBytes(file.size);
        recompress();
      }

      function updateQualityLabel() {
        const q = document.getElementById('qualitySlider').value;
        document.getElementById('qualityLabel').textContent = q + '%';
      }

      function recompress() {
        if (!originalFile) return;
        const quality = parseInt(document.getElementById('qualitySlider').value) / 100;
        const maxW = 1400; // max width in px

        const reader = new FileReader();
        reader.onload = function(e) {
          const img = new Image();
          img.onload = function() {
            let w = img.width, h = img.height;
            if (w > maxW) { h = Math.round(h * maxW / w); w = maxW; }

            const canvas = document.createElement('canvas');
            canvas.width  = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            const mimeType = canvas.toDataURL('image/webp').startsWith('data:image/webp')
              ? 'image/webp' : 'image/jpeg';

            canvas.toBlob(function(blob) {
              compressedBlob = blob;
              const dataUrl = canvas.toDataURL(mimeType, quality);

              document.getElementById('img_base64').value = dataUrl;
              document.getElementById('imgPreview').src = dataUrl;

              const saved = originalFile.size - blob.size;
              const pct   = Math.round((saved / originalFile.size) * 100);
              document.getElementById('sizeCompressed').textContent = formatBytes(blob.size);
              document.getElementById('sizeSaved').textContent =
                saved > 0 ? `${formatBytes(saved)} (${pct}% أصغر)` : 'لا فرق';
            }, mimeType, quality);
          };
          img.src = e.target.result;
        };
        reader.readAsDataURL(originalFile);
      }

      function removeImage() {
        originalFile = null;
        compressedBlob = null;
        document.getElementById('imgPicker').value = '';
        document.getElementById('img_base64').value = '';
        document.getElementById('img_removed').value = '1';
        document.getElementById('imgPreview').src = '';
        document.getElementById('imgPreviewBox').classList.add('d-none');
        document.getElementById('qualityRow').classList.add('d-none');
        document.getElementById('sizeOriginal').textContent = '—';
        document.getElementById('sizeCompressed').textContent = '—';
        document.getElementById('sizeSaved').textContent = '—';

        const currentImgLabel = document.getElementById('currentImgLabel');
        if (currentImgLabel) currentImgLabel.remove();
      }

      function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
      }
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
