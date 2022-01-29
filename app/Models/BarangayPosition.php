<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayPosition extends Model
{
    use HasFactory;

    protected $table = 'barangay_position';
    protected $dateformat = 'Y-m-d H:i:s';
    public $timestamps = true;

    protected $fillable = [
        'description'
    ];
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


}
