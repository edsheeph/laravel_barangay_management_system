<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalActiveCondition extends Model
{
    protected $table = 'medical_active_condition';
    public $timestamps = true;
    public $incrementing = true;
    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'disease_id',
        'active_medication'
    ];
}
