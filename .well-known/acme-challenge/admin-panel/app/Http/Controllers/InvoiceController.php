<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function list()
    {
        $data = Invoice::with('order.items')->orderBy('id','desc')->get();
        return response()->json(['ok'=>true,'data'=>$data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'order_id' => 'required|exists:orders,id',
            'date' => 'nullable|date',
            'amount' => 'nullable|numeric',
            'status' => 'required|string'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $data = $v->validated();
        // if amount not provided, use order amount
        if(empty($data['amount'])){
            $order = Order::find($data['order_id']);
            $data['amount'] = $order ? $order->amount : 0;
        }
        $i = Invoice::create($data);
        $i->load('order.items');
        return response()->json(['ok'=>true,'data'=>$i]);
    }

    public function show($id)
    {
        $i = Invoice::with('order')->findOrFail($id);
        return response()->json(['ok'=>true,'data'=>$i]);
    }

    // Render printable invoice view (web)
    public function view($id)
    {
        $i = Invoice::with('order')->findOrFail($id);
        return view('invoice.show', ['invoice' => $i]);
    }

    // Download invoice as PDF if dompdf is installed, otherwise return printable view
    public function download($id)
    {
        $i = Invoice::with('order.items')->findOrFail($id);
        // If barryvdh/laravel-dompdf is installed, use it
        if(class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')){
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice.show', ['invoice' => $i]);
            $filename = ($i->invoice_number ?: ('invoice-'.$i->id)).'.pdf';
            return $pdf->download($filename);
        }

        // fallback: return HTML printable view (user can print/save as PDF)
        return view('invoice.show', ['invoice' => $i]);
    }

    public function destroy($id)
    {
        Invoice::destroy($id);
        return response()->json(['ok'=>true]);
    }

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'invoice_number' => 'required|unique:invoices,invoice_number,'.$id,
            'order_id' => 'required|exists:orders,id',
            'date' => 'nullable|date',
            'amount' => 'nullable|numeric',
            'status' => 'required|string'
        ]);
        if($v->fails()) return response()->json(['ok'=>false,'errors'=>$v->errors()],422);
        $data = $v->validated();
        $i = Invoice::findOrFail($id);
        $i->update($data);
        $i->load('order.items');
        return response()->json(['ok'=>true,'data'=>$i]);
    }

    // Monthly invoice summary grouped by month
    public function summary(Request $request)
    {
        // Use date field if present, otherwise created_at
        $rows = Invoice::selectRaw("DATE_FORMAT(COALESCE(date,created_at),'%Y-%m') as ym, COUNT(*) as issued, SUM(CASE WHEN status='Paid' THEN 1 ELSE 0 END) as paid, SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending")->groupBy('ym')->orderBy('ym','desc')->get();

        $data = $rows->map(function($r){
            try{ $dt = Carbon::createFromFormat('Y-m',$r->ym); $month = $dt->format('M Y'); }
            catch(\Exception $e){ $month = $r->ym; }
            return ['month'=>$month,'issued'=> (int)$r->issued,'paid'=>(int)$r->paid,'pending'=>(int)$r->pending];
        })->values();

        return response()->json(['ok'=>true,'data'=>$data]);
    }
}
