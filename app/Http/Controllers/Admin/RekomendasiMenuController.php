<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\RekomendasiMenu; // Import model yang baru
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\DB;

class RekomendasiMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('kategori')->get();
        return view('rekomendasi.index', compact('menus'));
    }

    public function getRecommendationsForMenu(Request $request)
    {
        $selectedMenuIds = $request->input('selected_menus', []);

        if (empty($selectedMenuIds)) {
            return response()->json([
                'status' => true,
                'recommendations' => $this->getFallbackRecommendations([]),
                'algorithm_used' => 'default',
            ]);
        }

        $recommendations = [];
        $algorithmUsed = 'default';
        $recommendedMenuIds = [];

        // 1. Coba ambil rekomendasi dari Apriori (yang sudah dihitung sebelumnya)
        $aprioriRules = RekomendasiMenu::whereIn('menu_id', $selectedMenuIds)->get();

        if ($aprioriRules->isNotEmpty()) {
            foreach ($aprioriRules as $rule) {
                $recommendedMenuIds = array_merge($recommendedMenuIds, $rule->recommended_menu_ids);
            }
            $recommendedMenuIds = array_unique($recommendedMenuIds);

            $aprioriRecommendations = Menu::with('kategori')
                ->whereIn('id', $recommendedMenuIds)
                ->whereNotIn('id', $selectedMenuIds)
                ->take(3)
                ->get();
            
            if ($aprioriRecommendations->isNotEmpty()) {
                foreach ($aprioriRecommendations as $menu) {
                    $recommendations[] = $this->formatMenuData($menu);
                }
                $algorithmUsed = 'apriori';
            }
        }

        // 2. Jika Apriori tidak menemukan rule, gunakan metode fallback yang lebih cerdas
        if ($algorithmUsed === 'default') {
            $recommendations = $this->getFallbackRecommendations($selectedMenuIds);
        }

        return response()->json([
            'status' => true,
            'recommendations' => $recommendations,
            'algorithm_used' => $algorithmUsed,
        ]);
    }

    private function getFallbackRecommendations(array $selectedMenuIds): array
    {
        if (empty($selectedMenuIds)) {
            $popularMenus = DetailPesanan::select('menu_id', DB::raw('count(*) as total_pesanan'))
                                        ->groupBy('menu_id')
                                        ->orderByDesc('total_pesanan')
                                        ->with('menu.kategori')
                                        ->whereHas('menu')
                                        ->take(3)
                                        ->get();
            $formattedMenus = [];
            foreach ($popularMenus as $item) {
                if ($item->menu) {
                    $formattedMenus[] = $this->formatMenuData($item->menu);
                }
            }
            return $formattedMenus;
        }

        $selectedKategoriIds = Menu::whereIn('id', $selectedMenuIds)->pluck('kategori_id')->toArray();
        $selectedKategoriIds = array_unique($selectedKategoriIds);
        
        $popularMenus = DetailPesanan::select('menu_id', DB::raw('count(*) as total_pesanan'))
            ->groupBy('menu_id')
            ->orderByDesc('total_pesanan')
            ->with('menu.kategori')
            ->whereNotIn('menu_id', $selectedMenuIds)
            ->whereHas('menu', function($q) use ($selectedKategoriIds) {
                $q->whereNotIn('kategori_id', $selectedKategoriIds);
            })
            ->take(3)
            ->get();
        
        $formattedMenus = [];
        foreach ($popularMenus as $item) {
            if ($item->menu) {
                $formattedMenus[] = $this->formatMenuData($item->menu);
            }
        }
        
        if (count($formattedMenus) < 3) {
            $remainingCount = 3 - count($formattedMenus);
            $additionalMenus = Menu::with('kategori')
                ->whereNotIn('id', $selectedMenuIds)
                ->whereNotIn('id', array_column($formattedMenus, 'id'))
                ->inRandomOrder()
                ->take($remainingCount)
                ->get();
            
            foreach ($additionalMenus as $menu) {
                $formattedMenus[] = $this->formatMenuData($menu);
            }
        }

        return $formattedMenus;
    }

    private function formatMenuData($menu): array
    {
        return [
            'id' => $menu->id,
            'nama_menu' => $menu->nama_menu,
            'harga' => $menu->harga,
            'gambar' => $menu->gambar,
            'kategori' => $menu->kategori->nama_kategori ?? '',
        ];
    }
}