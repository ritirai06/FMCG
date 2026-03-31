<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invoice {{ $invoice->invoice_number }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f6f8fa;font-family:Inter,system-ui,Arial}
    .invoice-box{max-width:900px;margin:30px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 10px 30px rgba(2,6,23,.08)}
    .table td, .table th{vertical-align:middle}
    .no-print{display:inline-block}
    @media print{ .no-print{display:none} }
  </style>
</head>
<body>
  <div class="invoice-box">
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h4 class="mb-0">Company Name</h4>
        <div class="text-muted">Address line 1<br>City, State</div>
      </div>
      <div class="text-end">
        <h5 class="mb-0">Invoice</h5>
        <div class="text-muted">{{ $invoice->invoice_number }}</div>
        <div class="text-muted">Date: {{ $invoice->date ? $invoice->date->format('Y-m-d') : $invoice->created_at->format('Y-m-d') }}</div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-6">
        <strong>Bill To</strong>
        <div>{{ $invoice->order ? $invoice->order->customer : '' }}</div>
        <div class="text-muted">Store: {{ $invoice->order ? ($invoice->order->store ?? '') : '' }}</div>
      </div>
      <div class="col-6 text-end">
        <strong>Order</strong>
        <div>{{ $invoice->order ? $invoice->order->order_number : '' }}</div>
        <div class="text-muted">Order Date: {{ $invoice->order ? ($invoice->order->order_date ? $invoice->order->order_date->format('Y-m-d') : '') : '' }}</div>
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Item</th>
          <th class="text-end">Quantity</th>
          <th class="text-end">Unit Price</th>
          <th class="text-end">Total</th>
        </tr>
      </thead>
      <tbody>
        @if($invoice->order && $invoice->order->items && $invoice->order->items->count())
          @foreach($invoice->order->items as $it)
            <tr>
              <td>{{ $it->product_name }}</td>
              <td class="text-end">{{ $it->quantity }}</td>
              <td class="text-end">₹{{ number_format($it->unit_price,2) }}</td>
              <td class="text-end">₹{{ number_format($it->total,2) }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td>Order Payment</td>
            <td class="text-end">1</td>
            <td class="text-end">₹{{ number_format($invoice->amount,2) }}</td>
            <td class="text-end">₹{{ number_format($invoice->amount,2) }}</td>
          </tr>
        @endif
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Subtotal</th>
          <th class="text-end">₹{{ number_format($invoice->amount,2) }}</th>
        </tr>
        <tr>
          <th colspan="3" class="text-end">Total</th>
          <th class="text-end">₹{{ number_format($invoice->amount,2) }}</th>
        </tr>
      </tfoot>
    </table>

    <div class="d-flex justify-content-between mt-4">
      <div class="text-muted">Status: <strong>{{ $invoice->status }}</strong></div>
      <div>
        <a class="btn btn-outline-secondary no-print" href="javascript:window.print()"><i class="bi bi-download"></i> Print / Save as PDF</a>
        <a class="btn btn-primary ms-2 no-print" href="/orders"><i class="bi bi-arrow-left"></i> Back</a>
      </div>
    </div>
  </div>
</body>
</html>