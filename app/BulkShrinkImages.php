<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BulkShrinkImages
 *
 * @property int $id
 * @property int $bulk_shrink_id
 * @property string $url
 * @property string $data
 * @property int $status
 * @property string $error_message
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereBulkShrinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereErrorMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereUrl($value)
 * @mixin \Eloquent
 * @property int $file_id
 * @method static \Illuminate\Database\Query\Builder|\App\BulkShrinkImages whereFileId($value)
 * @property-read \App\BulkShrink $bulkShrink
 * @property-read \App\File $file
 */
class BulkShrinkImages extends Model
{
    const CREATED = 0;
    const PROCESSING = 1;
    const SUCCESS = 2;
    const SUCCESS_NOT_CHARGED = 3;
    const FAILED = 20;
    const NOT_IMAGE = 30;
    const TOO_BIG = 401;
    const NOT_FOUND = 404;
    const TIMEOUT = 408;
    const SERVER_ERROR = 500;

    protected $fillable = [
        'bulk_shrink_id',
        'file_id',
        'url',
        'data',
        'status',
        'error_message',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function bulkShrink()
    {
        return $this->belongsTo(BulkShrink::class);
    }



}
