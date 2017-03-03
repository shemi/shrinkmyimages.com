<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Subscription
 *
 * @property int $id
 * @property string $email
 * @property string $lists
 * @property bool $email_sent
 * @property string $sent_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereEmailSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereLists($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereSentAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
