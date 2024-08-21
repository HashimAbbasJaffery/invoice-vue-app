<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Counter;

class InvoiceController extends Controller
{
    public function get_all_invoices() {

        $invoices = Invoice::with("customer")->get();

        return response()->json([
            "invoices" => $invoices
        ], 200);
    }

    public function search(Request $request) {
        $keyword = $request->keyword;
        $invoices = Invoice::with("customer")->get();
        if($keyword != null) {

            $invoices = Invoice::with("customer")
                        ->where(function($query) use($keyword) {
                            $query->where("id", "like", "%$keyword%")
                            ->orWhereHas("customer", function($query) use($keyword) {
                                $query->where("firstname", "like", "%$keyword%");
                            });
                        })
                        ->get();
        }
        return response()->json([
            "invoices" => $invoices
        ], 200);
    }

    public function create_invoice(Request $request) {
        $counter = Counter::where("key", "invoice")->first();
        
        $invoice = Invoice::orderBy("id", "DESC")->first();
        $counters = "";

        if($invoice) {
            $invoice = $invoice->id + 1;
            $counters = $counter->prefix . ($counter->value + $invoice);
        } else {
            $counters = $counter->prefix . $counter->value;
        }

        $formData = [
            "number" => $counters,
            'customer_id' => null, 
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'terms_and_conditions' => 'Default terms and conditions',
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 1
                ]
            ]
        ];
        return response()->json($formData);
    }
}