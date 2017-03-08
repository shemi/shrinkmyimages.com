<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


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
