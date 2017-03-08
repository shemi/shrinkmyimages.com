<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'email'
    ];

    public function getListsAttribute($value)
    {
        if(! $value) {
            return [];
        }

        return explode(',', $value);
    }

    public function setListsAttribute($value)
    {
        if(is_string($value)) {
            $value = [$value];
        }

        $this->attributes['lists'] = implode(',', $value);
    }

}
