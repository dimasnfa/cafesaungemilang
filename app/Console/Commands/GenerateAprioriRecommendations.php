<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\RekomendasiMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateAprioriRecommendations extends Command
{
    protected $signature = 'rekomendasi:generate';
    protected $description = 'Menghitung dan menyimpan rekomendasi menu menggunakan algoritma Apriori.';

    public function handle()
    {
        $this->info('Memulai kalkulasi rekomendasi Apriori...');
        Log::info('Memulai kalkulasi rekomendasi Apriori...');

        // Ambil semua transaksi dinein
        $transactions = $this->getTransactions('dinein');
        
        if (empty($transactions)) {
            $this->warn('Tidak ada data transaksi ditemukan. Proses dihentikan.');
            Log::warning('Tidak ada data transaksi ditemukan untuk Apriori.');
            return 0;
        }

        $minSupport = 0.05; // Sesuaikan jika data transaksi masih sedikit
        $minConfidence = 0.3; // Sesuaikan jika data transaksi masih sedikit

        // Jalankan algoritma Apriori
        $frequentItemsets = $this->aprioriAlgorithm($transactions, $minSupport);
        $associationRules = $this->generateAssociationRules($frequentItemsets, $transactions, $minConfidence);

        // Hapus semua rekomendasi lama sebelum menyimpan yang baru
        RekomendasiMenu::truncate();

        // Simpan aturan asosiasi ke database
        DB::beginTransaction();
        try {
            foreach ($associationRules as $rule) {
                $antecedentNames = $rule['antecedent'];
                $consequentNames = $rule['consequent'];

                // Cari ID menu berdasarkan nama
                $antecedentMenus = Menu::whereIn('nama_menu', $antecedentNames)->pluck('id');
                $consequentMenus = Menu::whereIn('nama_menu', $consequentNames)->pluck('id');
                
                // Simpan setiap menu antecedent dengan consequent-nya
                foreach ($antecedentMenus as $menuId) {
                    RekomendasiMenu::create([
                        'menu_id' => $menuId,
                        'recommended_menu_ids' => $consequentMenus->toArray(),
                    ]);
                }
            }
            DB::commit();
            $this->info('Rekomendasi Apriori berhasil dibuat dan disimpan.');
            Log::info('Rekomendasi Apriori berhasil dibuat dan disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Gagal menyimpan rekomendasi: ' . $e->getMessage());
            Log::error('Gagal menyimpan rekomendasi Apriori: ' . $e->getMessage());
        }

        return 0;
    }

    private function getTransactions(string $jenisPesanan): array
    {
        $query = DetailPesanan::with(['pesanan', 'menu'])
            ->whereHas('pesanan', fn ($q) => $q->where('jenis_pesanan', $jenisPesanan))
            ->get()
            ->groupBy('pesanan_id');

        $transactions = [];
        foreach ($query as $items) {
            $transaction = [];
            foreach ($items as $item) {
                if ($item->menu) {
                    $transaction[] = $item->menu->nama_menu;
                }
            }
            $transactions[] = array_unique($transaction);
        }
        return $transactions;
    }

    private function aprioriAlgorithm(array $transactions, float $minSupport): array
    {
        $itemSupport = [];
        $totalTransactions = count($transactions);
        foreach ($transactions as $transaction) {
            foreach ($transaction as $item) {
                $itemSupport[$item] = ($itemSupport[$item] ?? 0) + 1;
            }
        }
    
        $frequentItemsets = [];
        foreach ($itemSupport as $item => $count) {
            $support = $count / $totalTransactions;
            if ($support >= $minSupport) {
                $frequentItemsets[serialize([$item])] = [
                    'items' => [$item],
                    'support' => $support,
                ];
            }
        }
    
        $k = 2;
        do {
            $newItemsets = [];
            $itemsets = array_values(array_map(fn($v) => $v['items'], $frequentItemsets));
            $itemsets = array_unique($itemsets, SORT_REGULAR);
    
            for ($i = 0; $i < count($itemsets); $i++) {
                for ($j = $i + 1; $j < count($itemsets); $j++) {
                    $candidate = array_unique(array_merge($itemsets[$i], $itemsets[$j]));
                    sort($candidate);
                    if (count($candidate) == $k) {
                        $candidateKey = serialize($candidate);
                        if (!isset($newItemsets[$candidateKey])) {
                            $support = $this->calculateSupport($candidate, $transactions);
                            if ($support >= $minSupport) {
                                $newItemsets[$candidateKey] = [
                                    'items' => $candidate,
                                    'support' => $support,
                                ];
                            }
                        }
                    }
                }
            }
    
            if (empty($newItemsets)) {
                break;
            }
    
            foreach ($newItemsets as $key => $itemset) {
                $frequentItemsets[$key] = $itemset;
            }
    
            $k++;
        } while (true);
    
        return $frequentItemsets;
    }

    private function generateAssociationRules(array $frequentItemsets, array $transactions, float $minConfidence): array
    {
        $rules = [];
        foreach ($frequentItemsets as $itemset) {
            $items = $itemset['items'];
            if (count($items) < 2) continue;

            $subsets = $this->getNonEmptySubsets($items);

            foreach ($subsets as $antecedent) {
                $consequent = array_values(array_diff($items, $antecedent));
                if (empty($consequent)) continue;

                $supportAB = $itemset['support'];
                $supportA = $this->calculateSupport($antecedent, $transactions);
                
                $confidence = ($supportA > 0) ? $supportAB / $supportA : 0;

                if ($confidence >= $minConfidence) {
                    $supportB = $this->calculateSupport($consequent, $transactions);
                    $lift = ($supportB > 0) ? $confidence / $supportB : 0;
                    
                    $rules[] = [
                        'antecedent' => array_values($antecedent),
                        'consequent' => $consequent,
                        'support' => $supportAB,
                        'confidence' => $confidence,
                        'lift' => $lift,
                    ];
                }
            }
        }
        return $rules;
    }

    private function calculateSupport(array $items, array $transactions): float
    {
        $count = 0;
        foreach ($transactions as $transaction) {
            if (empty(array_diff($items, $transaction))) {
                $count++;
            }
        }
        $totalTransactions = count($transactions);
        return $totalTransactions > 0 ? $count / $totalTransactions : 0;
    }

    private function getNonEmptySubsets(array $set): array
    {
        $results = [];
        $count = count($set);
        $subsetCount = 1 << $count;

        for ($i = 1; $i < $subsetCount - 1; $i++) {
            $subset = [];
            for ($j = 0; $j < $count; $j++) {
                if ($i & (1 << $j)) {
                    $subset[] = $set[$j];
                }
            }
            $results[] = $subset;
        }
        return $results;
    }
}