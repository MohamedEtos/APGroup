@extends('layout.app')

@section('content')

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">

        {{-- Card Header --}}
        <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
          <h6 class="mb-0">جدول الفواتير</h6>
          <div class="d-flex align-items-center gap-2 flex-wrap ">
            {{-- Rows per page --}}
            <div class="d-flex align-items-center gap-1 ">
                <label for="rowsPerPage" class="text-xs text-secondary mb-0 text-nowrap">عدد الصفوف:</label>
                <select dir="ltr" id="rowsPerPage" class="form-select " style="width:120px;" onchange="changeRowsPerPage()">
                  <option value="5">5</option>
                  <option value="10">10</option>
                  <option selected value="25">25</option>
                  <option value="50">50</option>
                  <option value="all">الكل</option>
                </select>
                  {{-- Live Search --}}
              <div class="input-group" style="min-width:220px;">
 
                <input
                  type="text"
                  id="liveSearchInput"
                  class="form-control border-start-0 ps-0"
                  placeholder="بحث لحظي..."
                  oninput="filterTable()"
                >
              </div>
            </div>
          </div>
        </div>

        {{-- Table --}}
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="invoicesTable">
              <thead>
                <tr>
                  <th class=" font-weight-bolder  text-end pe-3">#id</th>
                  <th class=" font-weight-bolder  text-end pe-3">رقم الفاتورة</th>
                  <th class=" font-weight-bolder  text-end pe-3">كود التوب</th>
                  <th class=" font-weight-bolder  text-center">صورة</th>
                  <th class=" font-weight-bolder  text-end pe-3">المسلم</th>
                  <th class=" font-weight-bolder  text-end pe-3">المستلم</th>
                  <th class=" font-weight-bolder  text-center">الكمية</th>
                  <th class=" font-weight-bolder  text-center">النوع</th>
                  <th class=" font-weight-bolder  text-center">التاريخ</th>
                  <th class="  text-center">تحكم</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                {{-- Rows rendered by JS --}}
              </tbody>
            </table>
          </div>

          {{-- Pagination --}}
          <div class="d-flex justify-content-between align-items-center px-4 py-3 flex-wrap gap-2">
            <span id="paginationInfo" class="text-xs text-secondary"></span>
            <nav>
              <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
            </nav>
          </div>
        </div>

      </div>
    </div>
  </div>

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

<script>
// ======================================================
// Dummy Data — استبدلها ببيانات من Backend عند الجاهزية
// ======================================================
const allData = [
  { id: 1,  invoice: 'INV-1001', code: 'TOP-A1', img: '{{ asset("assets/img/team-2.jpg") }}', sender: 'أحمد علي',     receiver: 'محمد سامي',   qty: 10, type: 'طباعة',   date: '2024-01-15' },
  { id: 2,  invoice: 'INV-1002', code: 'TOP-B2', img: '{{ asset("assets/img/team-3.jpg") }}', sender: 'سارة حسن',     receiver: 'خالد منصور',  qty: 5,  type: 'ليزر',    date: '2024-01-18' },
  { id: 3,  invoice: 'INV-1003', code: 'TOP-C3', img: '{{ asset("assets/img/team-4.jpg") }}', sender: 'عمر إبراهيم',  receiver: 'نادية كمال',  qty: 20, type: 'رول بريس', date: '2024-02-01' },
  { id: 4,  invoice: 'INV-1004', code: 'TOP-D4', img: '{{ asset("assets/img/team-2.jpg") }}', sender: 'فاطمة محمود',  receiver: 'يوسف أحمد',  qty: 8,  type: 'طباعة',   date: '2024-02-10' },
  { id: 5,  invoice: 'INV-1005', code: 'TOP-E5', img: '{{ asset("assets/img/team-3.jpg") }}', sender: 'كريم عبدالله', receiver: 'ريم فاروق',  qty: 15, type: 'ليزر',    date: '2024-02-14' },
  { id: 6,  invoice: 'INV-1006', code: 'TOP-F6', img: '{{ asset("assets/img/team-4.jpg") }}', sender: 'منى صالح',     receiver: 'تامر عيسى',  qty: 3,  type: 'ستراس',   date: '2024-03-05' },
  { id: 7,  invoice: 'INV-1007', code: 'TOP-G7', img: '{{ asset("assets/img/team-2.jpg") }}', sender: 'علي حمدي',     receiver: 'شيماء نبيل', qty: 25, type: 'طباعة',   date: '2024-03-12' },
  { id: 8,  invoice: 'INV-1008', code: 'TOP-H8', img: '{{ asset("assets/img/team-3.jpg") }}', sender: 'نور الدين',    receiver: 'إيمان رضا',  qty: 7,  type: 'رول بريس', date: '2024-03-20' },
  { id: 9,  invoice: 'INV-1009', code: 'TOP-I9', img: '{{ asset("assets/img/team-4.jpg") }}', sender: 'حسام فتحي',   receiver: 'دينا حسين',  qty: 12, type: 'ليزر',    date: '2024-04-02' },
  { id: 10, invoice: 'INV-1010', code: 'TOP-J10',img: '{{ asset("assets/img/team-2.jpg") }}', sender: 'إسلام بدر',   receiver: 'مروة طه',    qty: 30, type: 'ستراس',   date: '2024-04-09' },
  { id: 11, invoice: 'INV-1011', code: 'TOP-K11',img: '{{ asset("assets/img/team-3.jpg") }}', sender: 'أسماء جمال',  receiver: 'وليد سعد',   qty: 9,  type: 'طباعة',   date: '2024-04-15' },
  { id: 12, invoice: 'INV-1012', code: 'TOP-L12',img: '{{ asset("assets/img/team-4.jpg") }}', sender: 'باسم ناجي',   receiver: 'غادة عمر',   qty: 18, type: 'ليزر',    date: '2024-05-01' },
];

let ROWS_PER_PAGE = 25;
let currentPage   = 1;
let filteredData  = [...allData];

// ---- Colour badge per type
const typeBadge = {
  'طباعة'   : 'bg-gradient-primary',
  'ليزر'    : 'bg-gradient-info',
  'رول بريس': 'bg-gradient-warning',
  'ستراس'   : 'bg-gradient-success',
};

function changeRowsPerPage() {
  const val = document.getElementById('rowsPerPage').value;
  ROWS_PER_PAGE = (val === 'all') ? Infinity : parseInt(val);
  currentPage = 1;
  renderTable();
}

function filterTable() {
  const q = document.getElementById('liveSearchInput').value.trim().toLowerCase();
  filteredData = allData.filter(r =>
    Object.values(r).some(v => String(v).toLowerCase().includes(q))
  );
  currentPage = 1;
  renderTable();
}

function renderTable() {
  const isAll  = !isFinite(ROWS_PER_PAGE);
  const start  = isAll ? 0 : (currentPage - 1) * ROWS_PER_PAGE;
  const end    = isAll ? filteredData.length : start + ROWS_PER_PAGE;
  const page   = filteredData.slice(start, end);
  const total  = filteredData.length;
  const pages  = Math.ceil(total / ROWS_PER_PAGE) || 1;

  // ---- Rows
  const tbody = document.getElementById('tableBody');
  if (page.length === 0) {
    tbody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-secondary">لا توجد نتائج</td></tr>`;
  } else {
    tbody.innerHTML = page.map(r => `
      <tr>
        <td class="text-end pe-3">
          <span class="text-xs font-weight-bold">${r.id}</span>
        </td>
        <td class="text-end pe-3">
          <span class="text-xs font-weight-bold text-primary">${r.invoice}</span>
        </td>
        <td class="text-end pe-3">
          <span class="">${r.code}</span>
        </td>
        <td class="text-center">
          <img src="${r.img}" class="avatar avatar-sm border-radius-md shadow" alt="img" style="object-fit:cover;">
        </td>
        <td class="text-end pe-3">
          <span class="text-xs font-weight-bold">${r.sender}</span>
        </td>
        <td class="text-end pe-3">
          <span class="text-xs font-weight-bold">${r.receiver}</span>
        </td>
        <td class="text-center">
          <span class="badge badge-sm bg-gradient-secondary">${r.qty}</span>
        </td>
        <td class="text-center">
          <span class="">${r.type}</span>
        </td>
        <td class="text-center">
          <span class="text-xs text-secondary font-weight-bold">${r.date}</span>
        </td>
        <td class="text-center">
          <button class="btn btn-sm btn-outline-info mb-0 me-1 py-1 px-2" title="عرض" onclick="alert('عرض #${r.id}')">
            <i class="fas fa-eye fa-xs"></i>
          </button>
          <button class="btn btn-sm btn-outline-warning mb-0 me-1 py-1 px-2" title="تعديل" onclick="alert('تعديل #${r.id}')">
            <i class="fas fa-edit fa-xs"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger mb-0 py-1 px-2" title="حذف" onclick="confirmDelete(${r.id})">
            <i class="fas fa-trash fa-xs"></i>
          </button>
        </td>
      </tr>
    `).join('');
  }

  // ---- Info text
  document.getElementById('paginationInfo').textContent =
    `عرض ${Math.min(start + 1, total)} - ${Math.min(end, total)} من ${total} سجل`;

  // ---- Pagination buttons
  const ul = document.getElementById('paginationControls');
  ul.innerHTML = '';

  // Prev
  const prev = document.createElement('li');
  prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
  prev.innerHTML = `<a class="page-link" href="javascript:;" onclick="goToPage(${currentPage - 1})">&#8249;</a>`;
  ul.appendChild(prev);

  // Pages
  for (let p = 1; p <= pages; p++) {
    const li = document.createElement('li');
    li.className = `page-item ${p === currentPage ? 'active' : ''}`;
    li.innerHTML = `<a class="page-link" href="javascript:;" onclick="goToPage(${p})">${p}</a>`;
    ul.appendChild(li);
  }

  // Next
  const next = document.createElement('li');
  next.className = `page-item ${currentPage === pages ? 'disabled' : ''}`;
  next.innerHTML = `<a class="page-link" href="javascript:;" onclick="goToPage(${currentPage + 1})">&#8250;</a>`;
  ul.appendChild(next);
}

function goToPage(p) {
  const pages = Math.ceil(filteredData.length / ROWS_PER_PAGE) || 1;
  if (p < 1 || p > pages) return;
  currentPage = p;
  renderTable();
}

function confirmDelete(id) {
  if (confirm(`هل أنت متأكد من حذف السجل رقم ${id}؟`)) {
    alert(`تم حذف السجل رقم ${id}`);
  }
}

// Initial render
renderTable();
</script>

@endsection