<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>فاتورة رقم {{ $invoice->invoice_number }} - ApGroup</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  @vite('resources/css/sections/invoice-receipt.css')
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
