<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;


/**
 * App\Shrink
 *
 * @property int $id
 * @property int $user_id
 * @property string $mode
 * @property int $max_width
 * @property int $max_height
 * @property int $status
 * @property int $before_total_size
 * @property int $after_total_size
 * @property \Carbon\Carbon $expire_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $type
 * @property int $total_files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @property-read \App\User $user
 * @property-read mixed $folder_path
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereAfterTotalSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereBeforeTotalSize($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereExpireAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereMaxHeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereMaxWidth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereTotalFiles($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Shrink whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $percent
 */
class Shrink extends Model
{

    const NEW_STATUS = 0;
    const DELETED_STATUS = 1;

    protected $dates = [
        'expire_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    public function deleteFolder()
    {
        Storage::deleteDirectory($this->folder_path);

        $this->status = Shrink::DELETED_STATUS;
        $this->save();
    }

    public function getPercentAttribute()
    {
        return round(100 - round(($this->after_total_size / $this->before_total_size) * 100, 2), 2);
    }

}
