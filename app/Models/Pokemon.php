<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pokemon extends \Illuminate\Database\Eloquent\Model{
    use HasFactory;
    protected $table = 'pokemones';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'tipo',
        'url',
        'hp',
        'defensa',
        'ataque',
        'rapidez',
        'id_user',
        'eliminado'
    ];
    protected $hidden = [
        'eliminado'
    ];
    public $timestamps = true;
    public function user(){
        return $this->belongsTo(User::class, 'id:_user');
    }
}