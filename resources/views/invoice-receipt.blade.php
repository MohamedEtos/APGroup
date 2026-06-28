<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>فاتورة رقم {{ $invoice->invoice }} - ApGroup</title>
  
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
      max-width: 800px;
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
      font-size: 14px;
      border-bottom: 2px solid #dee2e6;
    }

    .invoice-table td {
      padding: 15px;
      font-size: 14px;
      color: #525f7f;
      border-bottom: 1px solid #e9ecef;
      text-align: right;
    }

    .totals-box {
      width: 250px;
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
      max-width: 800px;
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

      .actions-bar {
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
        <h1 class="invoice-title">فاتورة كتابية</h1>
        <p style="margin: 4px 0 0 0; font-size: 13px; color: #8898aa; text-align: left;">رقم: {{ $invoice->invoice }}</p>
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

    @php
      $subtotal = 0;
      $hasItems = $invoice->items && is_array($invoice->items);
      if ($hasItems) {
          foreach ($invoice->items as $item) {
              $subtotal += ($item['qty'] ?? 0) * ($item['price'] ?? 0);
          }
      } else {
          $subtotal = $invoice->qty * $invoice->price;
      }
    @endphp

    {{-- Table --}}
    <table class="invoice-table">
      <thead>
        <tr>
          <th style="width: 5%;">#</th>
          <th>كود التوب</th>
          <th style="text-align: center;">النوع</th>
          <th style="text-align: center;">الكمية</th>
          <th style="text-align: left;">سعر الوحدة</th>
          <th style="text-align: left;">الإجمالي</th>
        </tr>
      </thead>
      <tbody>
        @if($hasItems)
          @foreach($invoice->items as $index => $item)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $item['code'] ?? '' }}</td>
              <td style="text-align: center;">{{ $item['type'] ?? '' }}</td>
              <td style="text-align: center;">{{ $item['qty'] ?? 0 }}</td>
              <td style="text-align: left;">{{ number_format($item['price'] ?? 0, 2) }} ج.م</td>
              <td style="text-align: left; font-weight: 700; color: #2b3553;">{{ number_format(($item['qty'] ?? 0) * ($item['price'] ?? 0), 2) }} ج.م</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td>1</td>
            <td>{{ $invoice->code }}</td>
            <td style="text-align: center;">{{ $invoice->type }}</td>
            <td style="text-align: center;">{{ $invoice->qty }}</td>
            <td style="text-align: left;">{{ number_format($invoice->price, 2) }} ج.م</td>
            <td style="text-align: left; font-weight: 700; color: #2b3553;">{{ number_format($invoice->qty * $invoice->price, 2) }} ج.م</td>
          </tr>
        @endif
      </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-box">
      <div class="total-row">
        <span>الإجمالي الفرعي:</span>
        <span>{{ number_format($subtotal, 2) }} ج.م</span>
      </div>
      <div class="total-row">
        <span>الضريبة (0%):</span>
        <span>0.00 ج.م</span>
      </div>
      <div class="total-row grand-total">
        <span>الإجمالي الكلي:</span>
        <span>{{ number_format($subtotal, 2) }} ج.م</span>
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
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</body>

</html>
