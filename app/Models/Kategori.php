<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';  // Pastikan sesuai dengan tabel di database

    protected $primaryKey = 'id'; // Jika primary key di tabel adalah 'id'

    protected $fillable = [
        'nama_kategori',
    ];

    // Relasi dengan Menu (One-to-Many)
    public function menus()
    {
        return $this->hasMany(Menu::class, 'id_kategori', 'id'); 
        // id_kategori di menu merujuk ke id di kategori
    }
}
