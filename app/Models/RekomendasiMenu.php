<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class RekomendasiMenu extends Model
    {
        use HasFactory;

        protected $table = 'rekomendasi_menu';

        protected $fillable = [
            'menu_id',
            'recommended_menu_ids',
        ];

        protected $casts = [
            'recommended_menu_ids' => 'array',
        ];

        public function menu()
        {
            return $this->belongsTo(Menu::class, 'menu_id');
        }
    }
