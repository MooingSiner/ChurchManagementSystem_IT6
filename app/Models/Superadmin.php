<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Superadmin extends Model
{
    protected $table = 'superadmins';
    protected $primaryKey = 'administrator_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'administrator_id',
        'permission',
    ];

    public function administrator()
    {
        return $this->belongsTo(Administrator::class, 'administrator_id', 'administrator_id');
    }
}
