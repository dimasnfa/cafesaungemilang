<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meja;
use Illuminate\Validation\Rule;
use Milon\Barcode\DNS2D;

class MejaController extends Controller
{
    public function index()
    {
        $mejas = Meja::all();
        return view('admin.pages.meja.index', compact('mejas'));
    }

    public function create()
    {
        return view('admin.pages.meja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_meja' => [
                'required',
                'integer',
                Rule::unique('meja')->where(function ($query) use ($request) {
                    return $query->where('tipe_meja', $request->tipe_meja)
                                 ->where('lantai', $request->lantai);
                }),
            ],
            'tipe_meja' => 'required|in:lesehan,meja cafe',
            'lantai' => 'required|in:1,2',
            'status' => 'nullable|in:tersedia,terisi,dibersihkan',
        ]);

        $mejaNomor = $request->nomor_meja;
        $tipeSlug = str_replace(' ', '_', strtolower($request->tipe_meja));
        $lantai = $request->lantai;

        // 1. Simpan data ke DB terlebih dahulu
        $meja = Meja::create([
            'nomor_meja' => $mejaNomor,
            'tipe_meja' => $request->tipe_meja,
            'lantai' => $lantai,
            'status' => $request->status ?? 'tersedia',
        ]);

        // 2. Gunakan URL deployment yang sudah di-deploy
        // Pastikan menggunakan HTTPS untuk kompatibilitas dengan Google Lens
        $baseUrl = 'https://gemilangcafesaung.wayangkedawon.my.id/';
        $fullUrl = $baseUrl . "/dinein/booking/" . $meja->id;

        // 3. Buat nama file QR unik
        $fileName = "booking_{$mejaNomor}_{$tipeSlug}_lantai{$lantai}.png";
        $filePath = public_path("qr_codes/{$fileName}");

        // 4. Pastikan folder qr_codes exists
        if (!file_exists(public_path('qr_codes'))) {
            mkdir(public_path('qr_codes'), 0755, true);
        }

        // 5. Generate QR dengan ukuran yang lebih besar untuk Google Lens
        $qr = new DNS2D();
        $qr->setStorPath(public_path('qr_codes/'));
        
        // Parameter tambahan untuk kualitas QR yang lebih baik
        $qrPng = $qr->getBarcodePNG($fullUrl, 'QRCODE', 10, 10); // Size 10x10 untuk kualitas tinggi
        file_put_contents($filePath, base64_decode($qrPng));

        // 6. Simpan path file QR ke database
        $meja->update([
            'qr_code' => "qr_codes/{$fileName}",
        ]);

        return redirect()->route('admin.meja.index')->with('success', 'Meja berhasil ditambahkan!');
    }

    public function edit(Meja $meja)
    {
        return view('admin.pages.meja.edit', compact('meja'));
    }

    public function update(Request $request, Meja $meja)
    {
        $request->validate([
            'nomor_meja' => [
                'required',
                'integer',
                Rule::unique('meja')->where(function ($query) use ($request) {
                    return $query->where('tipe_meja', $request->tipe_meja)
                                 ->where('lantai', $request->lantai);
                })->ignore($meja->id),
            ],
            'tipe_meja' => 'required|in:lesehan,meja cafe',
            'lantai' => 'required|in:1,2',
            'status' => 'nullable|in:tersedia,terisi,dibersihkan',
        ]);

        // Jika nomor meja berubah, regenerate QR code
        if ($meja->nomor_meja != $request->nomor_meja || 
            $meja->tipe_meja != $request->tipe_meja || 
            $meja->lantai != $request->lantai) {
            
            // Hapus QR code lama
            if ($meja->qr_code && file_exists(public_path($meja->qr_code))) {
                unlink(public_path($meja->qr_code));
            }

            // Update data meja
            $meja->update($request->only(['nomor_meja', 'tipe_meja', 'lantai', 'status']));

            // Generate QR code baru
            $mejaNomor = $request->nomor_meja;
            $tipeSlug = str_replace(' ', '_', strtolower($request->tipe_meja));
            $lantai = $request->lantai;

            $baseUrl = 'https://gemilangcafesaung.wayangkedawon.my.id';
            $fullUrl = $baseUrl . "/dinein/booking/" . $meja->id;

            $fileName = "booking_{$mejaNomor}_{$tipeSlug}_lantai{$lantai}.png";
            $filePath = public_path("qr_codes/{$fileName}");

            if (!file_exists(public_path('qr_codes'))) {
                mkdir(public_path('qr_codes'), 0755, true);
            }

            $qr = new DNS2D();
            $qr->setStorPath(public_path('qr_codes/'));
            $qrPng = $qr->getBarcodePNG($fullUrl, 'QRCODE', 10, 10);
            file_put_contents($filePath, base64_decode($qrPng));

            $meja->update([
                'qr_code' => "qr_codes/{$fileName}",
            ]);
        } else {
            // Hanya update status jika tidak ada perubahan yang memerlukan regenerate QR
            $meja->update($request->only(['status']));
        }

        return redirect()->route('admin.meja.index')->with('success', 'Meja berhasil diperbarui!');
    }

    public function destroy(Meja $meja)
    {
        if ($meja->qr_code && file_exists(public_path($meja->qr_code))) {
            unlink(public_path($meja->qr_code));
        }

        $meja->delete();
        return redirect()->route('admin.meja.index')->with('success', 'Meja berhasil dihapus!');
    }

    // Method tambahan untuk regenerate semua QR codes jika diperlukan
    public function regenerateAllQR()
    {
        $mejas = Meja::all();
        $baseUrl = 'https://gemilangcafesaung.wayangkedawon.my.id/';

        foreach ($mejas as $meja) {
            // Hapus QR lama jika ada
            if ($meja->qr_code && file_exists(public_path($meja->qr_code))) {
                unlink(public_path($meja->qr_code));
            }

            $mejaNomor = $meja->nomor_meja;
            $tipeSlug = str_replace(' ', '_', strtolower($meja->tipe_meja));
            $lantai = $meja->lantai;

            $fullUrl = $baseUrl . "/dinein/booking/" . $meja->id;
            $fileName = "booking_{$mejaNomor}_{$tipeSlug}_lantai{$lantai}.png";

            // langsung ke direktori 'qr_codes' di root project
            $dirPath = base_path('qr_codes');
            $filePath = $dirPath . '/' . $fileName;

            // buat folder jika belum ada
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0755, true);
            }

            
            $qr = new DNS2D();
            $qr->setStorPath($dirPath . '/');
            $qrPng = $qr->getBarcodePNG($fullUrl, 'QRCODE', 10, 10);

            // simpan file ke folder qr_codes
            file_put_contents($filePath, base64_decode($qrPng));

            $meja->update([
                'qr_code' => "qr_codes/{$fileName}",
            ]);
        }

        return redirect()->route('admin.meja.index')->with('success', 'Semua QR Code berhasil di-regenerate!');
    }
}