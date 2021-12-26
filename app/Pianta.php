<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pianta extends Model
{
    protected $table = 'Pianta';
    protected $fillable = ['codice_serra', 'nome', 'foto', 'luogo', 'stato'];
    public $timestamps = false;
}
