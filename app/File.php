<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * App\File
 *
 * @property int $id
 * @property int $shrink_id
 * @property string $name
 * @property string $md5_name
 * @property string $ext
 * @property int $size_before
 * @property int $size_after
 * @property int $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $directory
 * @property-read mixed $path
 * @property-read mixed $reduced_percentage
 * @property-read \App\Shrink $shrink
 * @method static \Illuminate\Database\Query\Builder|\App\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereExt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereMd5Name($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereShrinkId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereSizeAfter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereSizeBefore($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    const UPLOADED_STATUS = 0;
    const SHRINKED_STATUS = 1;
    const NO_EFFECT_STATUS = 2;
    const ERROR_STATUS = 3;

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

    public function getReducedPercentageAttribute()
    {
        if(! $this->size_before || ! $this->size_after) {
            return 0;
        }

        return 100 - round(($this->size_after / $this->size_before) * 100, 2);
    }

}
