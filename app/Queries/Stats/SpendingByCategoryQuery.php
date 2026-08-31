<?php

namespace App\Queries\Stats;

use App\Models\Category;
use App\Models\Transaction;
use App\Support\PageCache;
use Carbon\Carbon;

// Returns spending breakdown by category for a given month, sorted by highest spend.
// Categories with zero spending are excluded.
// Each entry includes name, emoji, amount and pct (percentage of total monthly spending).
class SpendingByCategoryQuery
{
    public function __construct(
        private int $year,
        private int $month,
    ) {}

    public function handle(): array
    {
        return PageCache::remember("spending:{$this->year}:{$this->month}", 180, function () {
            $start = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $amounts = Transaction::query()
                ->selectRaw('category_id, SUM(amount) as total')
                ->where('type', 'expense')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->groupBy('category_id')
                ->pluck('total', 'category_id');

            if ($amounts->isEmpty()) {
                return [];
            }

            $categories = Category::where('is_goal', false)
                ->whereIn('id', $amounts->keys())
                ->get()
                ->map(fn ($cat) => [
                    'name' => $cat->name,
                    'emoji' => $cat->emoji,
                    'amount' => (float) ($amounts[$cat->id] ?? 0),
                ])
                ->filter(fn ($c) => $c['amount'] > 0)
                ->sortByDesc('amount')
                ->values()
                ->toArray();

            $total = array_sum(array_column($categories, 'amount'));

            return array_map(function ($c) use ($total) {
                $c['pct'] = $total > 0 ? round(($c['amount'] / $total) * 100) : 0;

                return $c;
            }, $categories);
        });
    }
}
