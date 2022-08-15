<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getNameAttribute($name)
    {
        return __("messages.$name");
    }

    public function Payment()
    {
        return $this->hasMany(Payment::class);
    }
}
