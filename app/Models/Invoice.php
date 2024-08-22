<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class Invoice extends Model
{
    use HasFactory;

    // protected $with = "customer";
    protected $guarded = ["id", "created_at", "updated_at"];
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function products() {
        return $this->belongsToMany(Product::class, "invoice_items")->withPivot("unit_price", "quantity");
    }
    public function scopeFilter(Builder $query, $filter) {
        $type = $filter["type"];
        $keyword = $filter["keyword"];

        // One means filter by customer
        if($type === '1') {
            $query->WhereHas("customer", function($query) use($keyword) {
                $query->where("firstname", "like", "%$keyword%");
            });
        }

        if($type == '2') {
            $query->where("number", "like" , "%$keyword%");
        }

        if($type == '3') {
            $query->where("total", "like", "%$keyword%");
        }

        if($type == '4') {
            $query->whereDate("date", $keyword);
        }

        if($type == '5') {
            $query->whereDate("due_date", $keyword);
        }


    }
}
