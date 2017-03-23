<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Credit
 *
 * @property int $id
 * @property int $user_id
 * @property string $origin
 * @property int $amount
 * @property string $description
 * @property int $price
 * @property string $currency
 * @property string $coupon_code
 * @property string $expired_at
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereCouponCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereExpiredAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereOrigin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Credit whereUserId($value)
 * @mixin \Eloquent
 */
class Credit extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

}
