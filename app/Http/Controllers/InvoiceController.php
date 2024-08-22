<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Counter;

class InvoiceController extends Controller
{
    public function get_all_invoices() {

        $invoices = Invoice::with("customer")->paginate(1);

        return response()->json([
            "invoices" => $invoices
        ], 200);
    }

    public function search(Request $request) {
        $keyword = $request->keyword;
        $type = $request->type;
        $is_paid = $request->is_paid;

        $invoices = Invoice::with("customer")->paginate(1);
        $filters = [
            "type" => $type,
            "keyword" => $keyword,
            "is_paid" => $is_paid
        ];

        // if($keyword !== null) {
            $invoices = Invoice::with("customer")
            ->filter($filters)
            ->paginate(1);
         // }

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
    public function store(InvoiceRequest $request) {
        $invoice_items = collect($request->invoice_items);

        $subTotal = $request->subTotal;
        $total = $request->total;

        $form = json_decode($request->form);

        $invoice = Invoice::create([
            "number" => $form->number,
            "date" => $form->date,
            "due_date" => $form->due_date,
            "reference" => $form->reference,
            "terms_and_conditions" => $form->terms_and_conditions,
            "sub_total" => $subTotal,
            "total" => $total,
            "discount" => $form->discount,
            "customer_id" => $form->customer_id
        ]);

        foreach($invoice_items as $item) {
            $invoice->products()->attach($item->id, [
                "quantity" => $item->quantity,
                "unit_price" => $item->unit_price
            ]);
        }
        return 1;
    }
    function edit(InvoiceRequest $request) {
        $invoice = Invoice::find($request->id);
        $invoice_items = collect($request->invoice_items);
        // return $invoice_items;

        $subTotal = $request->subTotal;
        $total = $request->total;

        $form = json_decode($request->form);

        $invoice->update([
            "number" => $form->number,
            "date" => $form->date,
            "due_date" => $form->due_date,
            "reference" => $form->reference,
            "terms_and_conditions" => $form->terms_and_conditions,
            "sub_total" => $subTotal,
            "total" => $total,
            "discount" => $form->discount,
            "customer_id" => $form->customer_id
        ]);
        // Detaching all of its previous products that were attached
        $invoice->products()->detach();

        foreach($invoice_items as $item) {
            $invoice->products()->attach($item->id, [
                "quantity" => $item->quantity,
                "unit_price" => $item->unit_price
            ]);
        }
        return 1;
    }
    public function get(Invoice $invoice) {
        return $invoice->load(["customer", "products"]);
    }
    public function delete(Invoice $invoice) {
        $invoice->products()->detach();
        $invoice->delete();
        return 1;
    }
}
