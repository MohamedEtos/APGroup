@extends('layout.app')

@section('content')

{{-- ===== Image Modal ===== --}}
<div id="imgModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.82); align-items:center; justify-content:center;" onclick="closeImgModal()">
  <div style="position:relative; max-width:90vw; max-height:90vh;" onclick="event.stopPropagation()">
    <button onclick="closeImgModal()" style="position:absolute; top:-14px; right:-14px; width:32px; height:32px; border-radius:50%; background:#fff; border:none; font-size:18px; line-height:1; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.3); z-index:1;">&times;</button>
    <img id="modalImg" src="" alt="صورة الفاتورة"
         style="max-width:88vw; max-height:88vh; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,.5); display:block;">
    <p id="modalCaption" style="text-align:center; color:#fff; margin-top:10px; font-size:13px; opacity:.8;"></p>
  </div>
</div>

<style>
  #imgModal.open { display:flex !important; }

  .img-thumb {
    width: 38px; height: 38px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
    cursor: zoom-in;
    transition: transform .18s ease, box-shadow .18s ease;
    border: 2px solid #e9ecef;
  }
  .img-thumb:hover { transform: scale(1.14); box-shadow: 0 4px 14px rgba(0,0,0,.22); }

  .img-placeholder {
    width: 38px; height: 38px;
    border-radius: 8px;
    background: linear-gradient(135deg, #f5f5f5, #e8e8e8);
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer;
    border: 2px dashed #ccc;
    color: #bbb;
    font-size: 15px;
    transition: all .18s ease;
  }
  .img-placeholder:hover { border-color: #cb0c9f; color: #cb0c9f; transform: scale(1.1); }

  /* Expandable items row */
  .invoice-detail-row { display: none; background: #f8f9fa; }
  .invoice-detail-row.open { display: table-row; }

  tr.invoice-main-row { cursor: pointer; user-select: none; }
  tr.invoice-main-row:hover td { background-color: rgba(203,12,159,.04); }

  .toggle-chevron {
    display: inline-block;
    transition: transform .22s ease;
    font-size: 0.65rem;
    color: #8392ab;
    margin-left: 5px;
  }
  .toggle-chevron.open { transform: rotate(90deg); }

  .detail-inner-table th {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #8392ab;
    padding: 6px 10px;
  }
  .detail-inner-table td {
    font-size: 0.8rem;
    padding: 7px 10px;
    border-bottom: 1px solid #ececec;
  }
  .detail-inner-table tr:last-child td { border-bottom: none; }
</style>

<div class="container-fluid py-4">
  
  {{-- Success Alert --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
      <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
      <span class="alert-text"><strong>نجاح!</strong> {{ session('success') }}</span>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card mb-4">

        {{-- Card Header --}}
        <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h6 class="mb-0">استلام الفواتير الجديدة (المعلقة)</h6>
            <p class="text-xs text-secondary mb-0">اضغط على أي صف لعرض تفاصيل الأصناف المطلوبة، أو اضغط على زر الاستلام للبدء بالمقارنة والتاكيد</p>
          </div>
          <div class="d-flex align-items-center gap-2 flex-nowrap">
            <input type="text" id="liveSearchInput" class="form-control form-control-sm" style="width:180px;" placeholder="بحث في الفواتير..." oninput="filterTable()">
          </div>
        </div>

        {{-- Table --}}
        <div class="card-body px-0 pt-0 pb-2 mt-3">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="invoicesTable">
              <thead>
                <tr>
                  <th class="font-weight-bolder" style="width:30px;"></th>
                  <th class="font-weight-bolder text-end pe-3">#</th>
                  <th class="font-weight-bolder text-end pe-3">رقم الفاتورة</th>
                  <th class="font-weight-bolder text-center">صورة</th>
                  <th class="font-weight-bolder text-end pe-3">المسلم</th>
                  <th class="font-weight-bolder text-end pe-3">المستلم (العميل)</th>
                  <th class="font-weight-bolder text-center">عدد الأصناف</th>
                  <th class="font-weight-bolder text-center">التاريخ</th>
                  <th class="font-weight-bolder text-center">الحالة</th>
                  <th class="text-center">استلام</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                {{-- Dynamic rows --}}
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
const allData = @json($invoices);
const PLACEHOLDER_SVG = `data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'><rect width='80' height='80' rx='10' fill='%23f0f0f0'/><text x='50%25' y='52%25' dominant-baseline='middle' text-anchor='middle' font-size='28' fill='%23bbb'>&#128247;</text></svg>`;

let filteredData = [...allData];

// Image Modal
function openImgModal(src, caption) {
  document.getElementById('modalImg').src = src;
  document.getElementById('modalCaption').textContent = caption || '';
  document.getElementById('imgModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeImgModal() {
  document.getElementById('imgModal').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeImgModal(); });

function imgCell(imgPath, caption) {
  const isDefault = !imgPath || imgPath === 'assets/img/team-2.jpg';
  if (isDefault) {
    return `<span class="img-placeholder" onclick="event.stopPropagation();openImgModal('${PLACEHOLDER_SVG}','لا توجد صورة')" title="لا توجد صورة"><i class="fas fa-image"></i></span>`;
  }
  const src = '/' + imgPath;
  return `<img src="${src}" class="img-thumb" alt="صورة"
    onclick="event.stopPropagation();openImgModal(this.src,'${caption}')"
    onerror="this.outerHTML='<span class=\\'img-placeholder\\' onclick=\\'event.stopPropagation();openImgModal(PLACEHOLDER_SVG,\\'لا توجد صورة\\')\\'><i class=\\'fas fa-image\\'></i></span>'"
    title="اضغط للتكبير">`;
}

function colorDot(color) {
  if (!color) return '—';
  return `<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${color};border:1px solid #ccc;vertical-align:middle;margin-left:3px;"></span> ${color}`;
}

function toggleDetail(id) {
  const detailRow = document.getElementById('detail-' + id);
  const chevron   = document.getElementById('chev-' + id);
  if (!detailRow) return;
  detailRow.classList.toggle('open');
  chevron.classList.toggle('open');
}

function filterTable() {
  const q = document.getElementById('liveSearchInput').value.trim().toLowerCase();
  filteredData = allData.filter(r =>
    [r.invoice_number, r.sender, r.receiver, r.date,
     ...(r.items || []).flatMap(i => [i.code, i.type])
    ].some(v => String(v || '').toLowerCase().includes(q))
  );
  renderTable();
}

function buildItemsTable(items) {
  if (!items || items.length === 0)
    return `<p class="text-xs text-secondary mb-0 p-2">لا توجد أصناف في هذه الفاتورة.</p>`;

  const rows = items.map((item, idx) => `
    <tr>
      <td>${idx + 1}</td>
      <td><strong>${item.code}</strong></td>
      <td>${item.type}</td>
      <td>${colorDot(item.fabric_color)}</td>
      <td class="text-center">${parseFloat(item.qty).toFixed(3)}</td>
      <td class="text-center"><span class="">${item.unit}</span></td>
      <td class="text-center font-weight-bold text-primary">${parseFloat(item.total_kg).toFixed(3)} كيلو</td>
    </tr>
  `).join('');

  return `
    <table class="table table-sm mb-0 detail-inner-table">
      <thead>
        <tr>
          <th>#</th><th>الكود</th><th>النوع</th><th>اللون</th>
          <th class="text-center">العدد المطلوب</th><th class="text-center">الوحدة</th>
          <th class="text-center">الوزن المطلوب</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>`;
}

function renderTable() {
  const tbody = document.getElementById('tableBody');
  if (filteredData.length === 0) {
    tbody.innerHTML = `<tr><td colspan="10" class="text-center py-5 text-secondary">
      <i class="fas fa-check-double fa-2x mb-2 d-block text-success opacity-8"></i>
      جميع الطلبات والفواتير مستلمة بالكامل!<br>
      <span class="text-xs text-muted">لا توجد فواتير قيد الاستلام حالياً.</span>
    </td></tr>`;
  } else {
    let html = '';
    filteredData.forEach((r, i) => {
      const itemCount = r.items ? r.items.length : 0;
      html += `
        <tr class="invoice-main-row" onclick="toggleDetail(${r.id})">
          <td class="text-center ps-3">
            <i class="fas fa-chevron-left toggle-chevron" id="chev-${r.id}"></i>
          </td>
          <td class="text-end pe-3"><span class="text-xs font-weight-bold">${i + 1}</span></td>
          <td class="text-end pe-3">
            <span class="text-xs font-weight-bold text-primary">${r.invoice_number}</span>
          </td>
          <td class="text-center py-2">${imgCell(r.img, r.invoice_number)}</td>
          <td class="text-end pe-3"><span class="text-xs font-weight-bold">${r.sender}</span></td>
          <td class="text-end pe-3"><span class="text-xs font-weight-bold">${r.receiver}</span></td>
          <td class="text-center"><span class="text-xs font-weight-bold">${itemCount} أصناف</span></td>
          <td class="text-center"><span class="text-xs text-secondary font-weight-bold">${r.date}</span></td>
          <td class="text-center">
            <span class="badge badge-sm bg-gradient-warning">قيد الاستلام</span>
          </td>
          <td class="text-center" onclick="event.stopPropagation()">
            <a href="/receive-invoices/${r.id}" class="btn btn-sm bg-gradient-primary mb-0 py-1 px-3" title="بدء الاستلام والمقارنة">
              <i class="fas fa-clipboard-check fa-xs me-1"></i> استلام
            </a>
          </td>
        </tr>
        <tr class="invoice-detail-row" id="detail-${r.id}">
          <td colspan="10" class="p-0">
            <div class="px-4 py-3">
              <small class="text-secondary font-weight-bold text-xs mb-2 d-block">
                <i class="fas fa-list-ul me-1"></i> تفاصيل الأصناف المطلوبة
              </small>
              ${buildItemsTable(r.items)}
            </div>
          </td>
        </tr>
      `;
    });
    tbody.innerHTML = html;
  }
}

renderTable();
</script>

@endsection
