<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\File
 *
 * @property-read \App\Shrink $shrink
 * @mixin \Eloquent
 * @property int $id
 * @property int $shrink_id
 * @property string $name
 * @property string $md5_name
 * @property string $ext
 * @property float $size_before
 * @property float $size_after
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereExt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereMd5Name($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereShrinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereSizeAfter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereSizeBefore($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereUpdatedAt($value)
 * @property-read mixed $directory
 * @property-read mixed $path
 * @property int $status
 * @method static \Illuminate\Database\Query\Builder|\App\File whereStatus($value)
 */
class File extends Model
{

    public function shrink()
    {
        return $this->belongsTo(Shrink::class);
    }

    public function getDirectoryAttribute()
    {
        return "shrinks/{$this->shrink_id}";
    }

    public function getPathAttribute()
    {
        return "{$this->directory}/{$this->md5_name}";
    }

}
