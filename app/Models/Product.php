<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "category_id",
        "vendor_id",
        "name",
        "slug",
        "sku",
        "description",
        "price",
        "compare_price",
        "stock",
        "image",
        "is_active",
        "featured",
        "is_archived",
        "archived_at",
        "was_active_before_subscription_expiry",
    ];

    protected $casts = [
        "price" => "decimal:2",
        "compare_price" => "decimal:2",
        "is_active" => "boolean",
        "featured" => "boolean",
        "is_archived" => "boolean",
        "archived_at" => "datetime",
        "was_active_before_subscription_expiry" => "boolean",
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, "vendor_id");
    }
}
