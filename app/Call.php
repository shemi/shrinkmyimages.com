<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Call
 *
 * @property int $id
 * @property int $user_id
 * @property int $shrink_id
 * @property string $type
 * @property string $from_ip
 * @property string $caller_identifier
 * @property int $status
 * @property string $extra_fields
 * @property int $credit
 * @property int $finish_after
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereCallerIdentifier($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereCredit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereExtraFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereFinishAfter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereFromIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereShrinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Call whereUserId($value)
 * @mixin \Eloquent
 */
class Call extends Model
{
    //
}
