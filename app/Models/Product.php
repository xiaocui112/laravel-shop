<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    /**
     * 平通商品
     */
    const TYPE_NORMAL = 'normal';
    /**
     * 众筹商品
     */
    const TYPE_CROWDFUNDING = 'crowdfunding';
    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
    ];
    protected $fillable = [
        'type',
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];
    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
    public function getImageFullAttribute()
    {
        return Storage::url($this->image);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class);
    }
}
