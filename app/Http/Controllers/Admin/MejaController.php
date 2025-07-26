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

        // 2. Bangun URL QR Code dengan aman (tanpa slash ganda)
        $baseUrl = config('app.webhook_url') ?? config('app.url');
        $cleanBaseUrl = rtrim($baseUrl, '/'); // pastikan tidak ada "/" di akhir
        $fullUrl = $cleanBaseUrl . "/dinein/booking/" . $meja->id;

        // 3. Buat nama file QR unik
        $fileName = "booking_{$mejaNomor}_{$tipeSlug}_lantai{$lantai}.png";
        $filePath = public_path("qr_codes/{$fileName}");

        // 4. Generate QR menggunakan milon/barcode
        $qr = new DNS2D();
        $qr->setStorPath(public_path('qr_codes/'));
        $qrPng = $qr->getBarcodePNG($fullUrl, 'QRCODE');
        file_put_contents($filePath, base64_decode($qrPng));

        // 5. Simpan path file QR ke database
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

        $meja->update($request->only(['nomor_meja', 'tipe_meja', 'lantai', 'status']));

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
}
