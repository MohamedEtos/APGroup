<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>فاتورة رقم {{ $invoice->invoice_number }} - ApGroup</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <style>
    *, *::before, *::after, body, html {
      font-family: 'Alexandria', 'Open Sans', sans-serif !important;
    }

    body {
      background-color: #f8f9fa;
      padding: 30px;
    }

    .invoice-card {
      max-width: 860px;
      margin: 0 auto;
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      border: 1px solid #e9ecef;
      position: relative;
    }

    .invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #dee2e6;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .logo-container img {
      max-height: 60px;
    }

    .invoice-title {
      font-size: 24px;
      font-weight: 700;
      color: #2b3553;
      margin: 0;
    }

    .info-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 40px;
      font-size: 14px;
    }

    .info-block h6 {
      font-weight: 700;
      color: #2b3553;
      margin-bottom: 10px;
      font-size: 15px;
      border-right: 3px solid #172b4d;
      padding-right: 8px;
    }

    .info-block p {
      margin: 4px 0;
      color: #525f7f;
    }

    .invoice-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    .invoice-table th {
      background-color: #f8f9fa;
      color: #2b3553;
      font-weight: 700;
      text-align: right;
      padding: 12px 15px;
      font-size: 13px;
      border-bottom: 2px solid #dee2e6;
    }

    .invoice-table td {
      padding: 12px 15px;
      font-size: 13px;
      color: #525f7f;
      border-bottom: 1px solid #e9ecef;
      text-align: right;
    }

    .color-dot {
      display: inline-block;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      border: 1px solid #ccc;
      vertical-align: middle;
      margin-left: 5px;
    }

    .unit-badge {
      display: inline-block;
      background: #e8f4fd;
      color: #1a73e8;
      font-size: 11px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 20px;
      border: 1px solid #c3e0f9;
    }

    .totals-box {
      width: 260px;
      margin-right: auto;
      margin-left: 0;
      border-top: 2px solid #dee2e6;
      padding-top: 15px;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      font-size: 14px;
      color: #525f7f;
    }

    .total-row.grand-total {
      font-size: 18px;
      font-weight: 700;
      color: #2b3553;
      border-top: 1px solid #dee2e6;
      padding-top: 10px;
      margin-top: 5px;
    }

    .footer-note {
      text-align: center;
      margin-top: 60px;
      font-size: 12px;
      color: #8898aa;
      border-top: 1px solid #e9ecef;
      padding-top: 20px;
    }

    .actions-bar {
      max-width: 860px;
      margin: 0 auto 20px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .btn {
      display: inline-block;
      font-weight: 600;
      text-align: center;
      vertical-align: middle;
      user-select: none;
      border: 1px solid transparent;
      padding: 8px 16px;
      font-size: 14px;
      line-height: 1.5;
      border-radius: 6px;
      transition: all 0.15s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-primary {
      color: #fff;
      background-color: #5e72e4;
      border-color: #5e72e4;
      box-shadow: 0 4px 6px rgba(50,50,93,0.11), 0 1px 3px rgba(0,0,0,0.08);
    }

    .btn-primary:hover {
      background-color: #324cdd;
      border-color: #324cdd;
    }

    .btn-secondary {
      color: #212529;
      background-color: #f7fafc;
      border-color: #f7fafc;
      box-shadow: 0 4px 6px rgba(50,50,93,0.11), 0 1px 3px rgba(0,0,0,0.08);
    }

    .btn-secondary:hover {
      background-color: #e2e8f0;
      border-color: #cbd5e0;
    }

    .toggle-container {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 25px;
    }

    .btn-toggle {
      border: 1px solid #dee2e6;
      background-color: #fff;
      color: #525f7f;
      padding: 8px 20px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 30px;
      transition: all 0.25s ease;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-toggle:hover {
      background-color: #f8f9fa;
      border-color: #cbd5e0;
    }

    .btn-toggle.active {
      background-color: #5e72e4;
      color: #fff;
      border-color: #5e72e4;
      box-shadow: 0 4px 6px rgba(50,50,93,0.11), 0 1px 3px rgba(0,0,0,0.08);
    }

    /* Print Stylesheet */
    @media print {
      body {
        background-color: #ffffff;
        padding: 0;
      }

      .invoice-card {
        box-shadow: none;
        border: none;
        padding: 0;
        max-width: 100%;
        margin: 0;
      }

      .actions-bar, .toggle-container {
        display: none !important;
      }
    }
  </style>
</head>

<body>

  <div class="actions-bar">
    <a href="{{ route('office-invoices.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-right"></i> العودة للفواتير
    </a>
    <button onclick="window.print()" class="btn btn-primary">
      <i class="fas fa-print"></i> طباعة الفاتورة
    </button>
  </div>

  <div class="invoice-card">
    {{-- Header --}}
    <div class="invoice-header">
      <div class="logo-container">
        <img src="{{ asset('assets/img/logos/ap.svg') }}" alt="ApGroup Logo">
      </div>
      <div>
        <h1 class="invoice-title">AP Group </h1>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #8898aa; text-align: left;">رقم: {{ $invoice->invoice_number }}</p>
      </div>
    </div>

    {{-- Info Section --}}
    <div class="info-section">
      <div class="info-block">
        <h6>معلومات العميل</h6>
        <p><strong>اسم العميل (المستلم):</strong> {{ $invoice->receiver }}</p>
        <p><strong>تاريخ الفاتورة:</strong> {{ $invoice->date }}</p>
      </div>
      <div class="info-block" style="text-align: left;">
        <h6>تفاصيل المصدر</h6>
        <p><strong>الجهة المصدرة:</strong> ApGroup المكتب الرئيسي</p>
        <p><strong>المسؤول (المسلم):</strong> {{ $invoice->sender }}</p>
      </div>
    </div>

    {{-- View Toggle Buttons --}}
    @if($invoice->status === 'received')
    <div class="toggle-container">
      <button type="button" id="btn-original" class="btn-toggle active" onclick="setView('original')">
        <i class="fas fa-file-invoice"></i> القيمة الأصلية المطلوبة
      </button>
      <button type="button" id="btn-delivered" class="btn-toggle" onclick="setView('delivered')">
        <i class="fas fa-clipboard-check"></i> القيمة المسلمة فعلياً
      </button>
    </div>
    @endif

    {{-- Items Table --}}
    <table class="invoice-table">
      <thead>
        <tr>
          <th style="width: 5%;">#</th>
          <th>كود التوب</th>
          <th style="text-align: center;">النوع</th>
          <th style="text-align: center;">لون القماش</th>
          <th style="text-align: center;">العدد</th>
          <th style="text-align: center;">الوحدة</th>
          <th style="text-align: center;">الإجمالي بالكيلو</th>
        </tr>
      </thead>
      <tbody>
        @forelse($invoice->items as $index => $item)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td><strong>{{ $item->code }}</strong></td>
            <td style="text-align: center;">{{ $item->type }}</td>
            <td style="text-align: center;">
              @if($item->fabric_color)
                <span class="color-dot" style="background: {{ $item->fabric_color }};"></span>
                {{ $item->fabric_color }}
              @else
                <span style="color:#ccc;">—</span>
              @endif
            </td>
            <td style="text-align: center;" class="item-qty" data-original="{{ number_format($item->qty, 3) }}" data-delivered="{{ number_format($item->delivered_qty ?? 0, 3) }}">{{ number_format($item->qty, 3) }}</td>
            <td style="text-align: center;"><span class="unit-badge">{{ $item->unit }}</span></td>
            <td style="text-align: center; font-weight: 700; color: #2b3553;" class="item-weight" data-original="{{ number_format($item->total_kg, 3) }} كيلو" data-delivered="{{ number_format($item->delivered_total_kg ?? 0, 3) }} كيلو">{{ number_format($item->total_kg, 3) }} كيلو</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; color: #aaa;">لا توجد أصناف في هذه الفاتورة.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Total KG box --}}
    <div class="totals-box">
      <div class="total-row grand-total">
        <span>إجمالي الكيلو:</span>
        <span id="grand-total" data-original="{{ number_format($invoice->items->sum('total_kg'), 3) }} كيلو" data-delivered="{{ number_format($invoice->items->sum('delivered_total_kg'), 3) }} كيلو">{{ number_format($invoice->items->sum('total_kg'), 3) }} كيلو</span>
      </div>
    </div>

    {{-- Signatures --}}
    <div style="display: flex; justify-content: space-between; margin-top: 80px; padding: 0 10px;">
      <div style="text-align: center; width: 200px; border-top: 1px solid #dee2e6; padding-top: 10px;">
        <span style="font-size: 13px; color: #8898aa;">توقيع المستلم (العميل)</span>
      </div>
      <div style="text-align: center; width: 200px; border-top: 1px solid #dee2e6; padding-top: 10px;">
        <span style="font-size: 13px; color: #8898aa;">توقيع المسؤول (المسلم)</span>
      </div>
    </div>

    {{-- Footer note --}}
    <div class="footer-note">
      <p>شكراً لتعاملكم معنا. تم إنشاء هذه الفاتورة تلقائياً من نظام ApGroup Dashboard.</p>
    </div>
  </div>

  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  @if($invoice->status === 'received')
  <script>
    function setView(type) {
      document.getElementById('btn-original').classList.toggle('active', type === 'original');
      document.getElementById('btn-delivered').classList.toggle('active', type === 'delivered');

      document.querySelectorAll('.item-qty').forEach(function(el) {
        el.textContent = type === 'original' ? el.getAttribute('data-original') : el.getAttribute('data-delivered');
      });

      document.querySelectorAll('.item-weight').forEach(function(el) {
        el.textContent = type === 'original' ? el.getAttribute('data-original') : el.getAttribute('data-delivered');
      });

      var totalEl = document.getElementById('grand-total');
      totalEl.textContent = type === 'original' ? totalEl.getAttribute('data-original') : totalEl.getAttribute('data-delivered');
    }
  </script>
  @endif
</body>

</html>
