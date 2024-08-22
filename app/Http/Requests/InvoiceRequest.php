<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Request;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "number" => ["required"],
            "customer_id" => ["required", "numeric"],
            "date" => [ "required" ],
            "due_date" => [ "required" ],
            "terms_and_conditions" => [ "required" ],
            "subTotal" => [ "required", "numeric" ],
            "total" => [ "required", "numeric" ],
        ];
    }

    protected function prepareForValidation() {
        $form = json_decode(request()->form);
        $invoice_items = json_decode(request()->invoice_items);

        $this->merge([
            "number" => $form->number,
            "customer_id" => $form->customer_id,
            "date" => $form->date,
            "due_date" => $form->due_date,
            "terms_and_conditions" => $form->terms_and_conditions,
            "discount" => $form->discount,
            "invoice_items" => $invoice_items
        ]);
    }
    public function messages() {
        return [
            "customer_id.required" => "Selection of one customer is required",
            "customer_id.numeric" => "Numeric value is expected"
        ];
    }
    public function withValidator($validator) {
        $validator->after(function($validator) {
            if(count($this->invoice_items) <= 0) {
                $validator->errors()->add("invoice_items", "You have to add at least one Product");
            }
        });
    }
}
