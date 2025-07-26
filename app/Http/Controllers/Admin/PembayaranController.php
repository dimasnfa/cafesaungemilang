<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('pesanan')->latest();

        // Filter berdasarkan jenis pesanan
        if ($request->has('jenis') && in_array($request->jenis, ['dinein', 'takeaway'])) {
            $query->where('jenis_pesanan', $request->jenis);
        }

        // Opsional: filter berdasarkan metode pembayaran
        if ($request->has('metode') && in_array($request->metode, ['qris', 'cash'])) {
            $query->where('metode_pembayaran', $request->metode);
        }

        $pembayarans = $query->paginate(10);

        return view('admin.pages.pembayaran.index', compact('pembayarans'));
    }

    public function create()
    {
        $pesanans = Pesanan::with('meja')->latest()->get();
        return view('admin.pages.pembayaran.create', compact('pesanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id'          => 'required|unique:pembayaran,order_id',
            'total_bayar'       => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:qris,cash',
            'status_pembayaran' => 'required|in:pending,dibayar,gagal,expired',
            'jenis_pesanan'     => 'required|in:dinein,takeaway',
            'nama_pelanggan'    => 'nullable|string|max:100',
            'nomor_wa'          => 'nullable|string|max:20',
            'pesanan_id'        => 'required|exists:pesanan,id',
        ]);

        $pesanan = Pesanan::with('meja')->findOrFail($request->pesanan_id);
        $nomorMeja = $pesanan->meja?->nomor_meja;

        Pembayaran::create([
            'pesanan_id'        => $request->pesanan_id,
            'order_id'          => $request->order_id,
            'total_bayar'       => $request->total_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran' => $request->status_pembayaran,
            'jenis_pesanan'     => $request->jenis_pesanan,
            'nama_pelanggan'    => $request->nama_pelanggan,
            'nomor_wa'          => $request->nomor_wa,
            'nomor_meja'        => $nomorMeja,
        ]);

        return redirect()->route('admin.pembayaran.index')->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pesanans = Pesanan::with('meja')->get();
        return view('admin.pages.pembayaran.edit', compact('pembayaran', 'pesanans'));
    }

    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'order_id'          => 'required|unique:pembayaran,order_id,' . $pembayaran->id,
            'total_bayar'       => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:qris,cash',
            'status_pembayaran' => 'required|in:pending,dibayar,gagal,expired',
            'jenis_pesanan'     => 'required|in:dinein,takeaway',
            'nama_pelanggan'    => 'nullable|string|max:100',
            'nomor_wa'          => 'nullable|string|max:20',
            'pesanan_id'        => 'required|exists:pesanan,id',
        ]);

        $pesanan = Pesanan::with('meja')->findOrFail($request->pesanan_id);
        $nomorMeja = $pesanan->meja?->nomor_meja;

        $pembayaran->update([
            'pesanan_id'        => $request->pesanan_id,
            'order_id'          => $request->order_id,
            'total_bayar'       => $request->total_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran' => $request->status_pembayaran,
            'jenis_pesanan'     => $request->jenis_pesanan,
            'nama_pelanggan'    => $request->nama_pelanggan,
            'nomor_wa'          => $request->nomor_wa,
            'nomor_meja'        => $nomorMeja,
        ]);

        return redirect()->route('admin.pembayaran.index')->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    
    public function invoice($id)
    {
        $pembayaran = Pembayaran::with(['pesanan.menu'])->findOrFail($id);
        return view('admin.pages.pembayaran.invoice', compact('pembayaran'));
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus.');
    }
}
