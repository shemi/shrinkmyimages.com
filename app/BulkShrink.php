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
 * @property string $callback_url
 * @property string $base_url
 * @property array $extra_fields
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $security_type
 * @property array $security_fields
 * @property-read \App\Call $call
 * @property-read \App\Shrink $shrink
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereBaseUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereCallId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereCallbackUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereExtraFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereSecurityFields($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereSecurityType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereShrinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrink whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BulkShrinkImages[] $images
 */
class BulkShrink extends Model
{

    const STATUS_CREATED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_FINISH = 2;
    const STATUS_CALLBACK_URL_ERRORS = [
        'GENERAL' => 100,
        'REDIRECT' => 300,
        'BAD_REQUEST' => 400,
        'FORBIDDEN' => 403,
        'NOT_FOUND' => 404,
        'TIMEOUT' => 408,
        'SERVER_ERROR' => 500,
    ];

    /**
     * @var BulkShrinkImages|null
     */
    protected $_nextImage = null;

    protected $fillable = [
        'shrink_id',
        'call_id',
        'status',
        'callback_url',
        'base_url',
        'extra_fields',
        'security_type',
        'security_fields',
    ];

    protected $casts = [
        'extra_fields' => 'array',
        'security_fields' => 'array',
        'status' => 'integer',
    ];

    public function call()
    {
        return $this->belongsTo(Call::class);
    }

    public function shrink()
    {
        return $this->belongsTo(Shrink::class);
    }

    public function images()
    {
        return $this->hasMany(BulkShrinkImages::class);
    }

    /**
     * @return bool
     */
    public function hasNextImage()
    {
        $this->_nextImage = $this->nextImage();

        return (bool) $this->_nextImage;
    }

    /**
     * @return BulkShrinkImages|null
     */
    public function nextImage()
    {
        if($this->_nextImage) {
            return $this->_nextImage;
        }

        return $this->images()
            ->where('status', BulkShrinkImages::CREATED)
            ->first();
    }

}
