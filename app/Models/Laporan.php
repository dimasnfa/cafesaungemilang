?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Laporan extends Model
// {
//     use HasFactory;

//     protected $table = 'laporan';

//     protected $fillable = [
//         'tanggal_laporan',
//         'total_pesanan',
//         'total_dinein',
//         'total_takeaway',
//         'total_pendapatan',
//         'total_pembayaran'
//     ];

//     public function pesanans()
//     {
//         return $this->hasMany(Pesanan::class, 'tanggal_pesanan', 'tanggal_laporan');
//     }

//     public function pembayarans()
//     {
//         return $this->hasMany(Pembayaran::class, 'created_at', 'tanggal_laporan');
//     }


//     public function detailPesanans()
//     {
//         return $this->hasManyThrough(
//             DetailPesanan::class,
//             Pesanan::class,
//             'tanggal_pesanan', 
//             'pesanan_id',      
//             'tanggal_laporan', 
//             'id'             
//         );
//     }

//     public function scopeHarian($query, $tanggal)
//     {
//         return $query->whereDate('tanggal_laporan', $tanggal);
//     }

//     public function scopeBulanan($query, $bulan, $tahun)
//     {
//         return $query->whereMonth('tanggal_laporan', $bulan)
//                      ->whereYear('tanggal_laporan', $tahun);
//     }

//     public function scopeTahunan($query, $tahun)
//     {
//         return $query->whereYear('tanggal_laporan', $tahun);
//     }
// }
