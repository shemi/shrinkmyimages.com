<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\BulkShrink
 *
 * @property int $id
 * @property int $shrink_id
 * @property int $call_id
 * @property int $status
 * @property string $security_token
 * @property string $callback_url
 * @property string $download_url
 * @property string $extra_fields
 * @property string $images
 * @property string $last_image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Call $call
 * @property-read \App\Shrink $shrink
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereCallId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereCallbackUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereDownloadUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereExtraFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereImages($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereLastImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereSecurityToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereShrinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BulkShrink extends Model
{

    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    public function shrink()
    {
        return $this->belongsTo(Shrink::class);
    }

}
