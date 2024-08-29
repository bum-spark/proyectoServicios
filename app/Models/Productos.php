<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Model;

class Productos extends \Illuminate\Database\Eloquent\Model{
    use HasFactory;
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'precio',
        'cantidad',
        'id_user',
        'eliminado'
    ];

    protected $hidden = [
        'eliminado',
        'create_at'
    ];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

}