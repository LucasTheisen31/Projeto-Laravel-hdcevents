<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        //definindo que o atributo 'items' é um array
        'items' => 'array'
    ];

    protected $dates = ['date'];

    protected $guarded = [];//para poder atualizar todos os dados

    //informa que o evento pertence a um usuario "ManyToOne"
    protected function user(){
        return $this->belongsTo('App\Models\User');
    }

    //informa que o evento pode estar associado a muitos participantes "ManyToMany"
    protected function users(){
        return $this->belongsToMany('App\Models\User');
    }
}
