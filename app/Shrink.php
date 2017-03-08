<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;


class Shrink extends Model
{

    const NEW_STATUS = 0;
    const DELETED_STATUS = 1;

    protected $dates = [
        'expire_at',
        'created_at',
        'updated_at'
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

    public function deleteFolder()
    {
        Storage::deleteDirectory($this->folder_path);

        $this->status = Shrink::DELETED_STATUS;
        $this->save();
    }

}
