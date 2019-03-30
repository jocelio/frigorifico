<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    protected $fillable = [
        'client_id', 'type', 'date', 'value','user_id'
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
