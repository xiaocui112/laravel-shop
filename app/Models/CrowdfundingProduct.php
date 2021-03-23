<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrowdfundingProduct extends Model
{
    use HasFactory;
    /**
     * 众筹中
     */
    const STATUS_FUNDING = 'funding';
    /** 
     * 众筹成功
     */
    const STATUS_SUCCESS = 'success';
    /**
     * 众筹失败
     */
    const STATUS_FAIL = 'fail';
    public static $statusMap = [
        self::STATUS_FUNDING => '众筹中',
        self::STATUS_SUCCESS => '众筹成功',
        self::STATUS_FAIL => '众筹失败',
    ];
    protected $fillable = ['total_amount', 'target_amount', 'user_count', 'status', 'end_at'];
    protected $dates = ['end_at'];
    public $timestamps = false;
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getPercentAttribute()
    {
        $value = $this->attributes['total_amount'] / $this->attributes['target_amount'];
        return floatval(number_format($value * 100, 2, '.', ''));
    }
}
