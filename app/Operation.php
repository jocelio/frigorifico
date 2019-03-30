<?php

namespace App;

use Akaunting\Money\Money;
use Carbon\Carbon;
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

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = str_replace(',', '.', str_replace('.', '', $value));
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromDate($value);
    }

    public function getFormattedDate()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }
}
