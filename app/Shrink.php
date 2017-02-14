<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Shrink
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $mode
 * @property int $max_width
 * @property int $max_height
 * @property int $status
 * @property float $before_total_size
 * @property float $after_total_size
 * @property string $expire_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereAfterTotalSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereBeforeTotalSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereExpireAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereMaxHeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereMaxWidth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereUserId($value)
 * @property-read mixed $folder_path
 */
class Shrink extends Model
{

    protected $dates = [
        'created_at',
        'updated_at',
        'expire_at'
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getMaxWidthAttribute($value)
    {
        if(! $value) {
            return null;
        }

        return $value < 6 ? null : $value;
    }

    public function getMaxHeightAttribute($value)
    {
        if(! $value) {
            return null;
        }

        return $value < 6 ? null : $value;
    }

    public function getFolderPathAttribute()
    {
        return "shrinks/{$this->id}";
    }

}
