<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    
    use HasFactory;
    protected $fillable = [
        'nom_fichier',
        'date_tranfert',
        'nbr_enregistrement',
        'user_name',
        'saved_in_db',
        'saved_in_table',
    ];
}
