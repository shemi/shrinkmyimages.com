<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


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
