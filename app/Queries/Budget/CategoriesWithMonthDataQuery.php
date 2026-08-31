<?php

namespace App\Queries\Budget;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

// Returns all non-goal categories enriched with budget data for the given month.
// Each category gets three computed properties:
//   - assigned:  budget amount set for this month
//   - spent:     total expenses in this month
//   - available: leftover balance carried forward from all prior months plus this
//                month's assigned, minus this month's spent (envelope rollover)
class CategoriesWithMonthDataQuery
{
    public function __construct(
        private int $year,
        private int $month,
    ) {}

    public function handle(): Collection
    {
        $monthStart = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $cutoff = $monthStart->copy()->endOfMonth();

        $categories = Category::where('is_goal', false)
            ->orderBy('sort_order')
            ->get();

        if ($categories->isEmpty()) {
            return $categories;
        }

        $categoryIds = $categories->pluck('id');

        $monthlyAssigned = Budget::query()
            ->selectRaw('category_id, SUM(amount) as total')
            ->whereIn('category_id', $categoryIds)
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $monthlySpent = Transaction::query()
            ->selectRaw('category_id, SUM(amount) as total')
            ->whereIn('category_id', $categoryIds)
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart->toDateString(), $cutoff->toDateString()])
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $cumulativeAssigned = Budget::query()
            ->selectRaw('category_id, SUM(amount) as total')
            ->whereIn('category_id', $categoryIds)
            ->where(function ($q) {
                $q->where('year', '<', $this->year)
                    ->orWhere(function ($q2) {
                        $q2->where('year', $this->year)->where('month', '<=', $this->month);
                    });
            })
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $cumulativeSpent = Transaction::query()
            ->selectRaw('category_id, SUM(amount) as total')
            ->whereIn('category_id', $categoryIds)
            ->where('type', 'expense')
            ->where('date', '<=', $cutoff->toDateString())
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return $categories->map(function ($category) use ($monthlyAssigned, $monthlySpent, $cumulativeAssigned, $cumulativeSpent) {
            $assigned = (float) ($monthlyAssigned[$category->id] ?? 0);
            $spent = (float) ($monthlySpent[$category->id] ?? 0);
            $available = (float) ($cumulativeAssigned[$category->id] ?? 0) - (float) ($cumulativeSpent[$category->id] ?? 0);
            $totalBudget = $assigned;
            $pct = $totalBudget > 0 ? min(100, round($spent / $totalBudget * 100)) : 0;

            $category->assigned = $assigned;
            $category->spent = $spent;
            $category->available = $available;
            $category->total_budget = $totalBudget;
            $category->pct = $pct;
            $category->bar_color = $available < 0 ? 'bg-red-500' : ($pct >= 80 ? 'bg-yellow-500' : 'bg-green-500');
            $category->bar_color_mobile = $available < 0 ? 'bg-red-400' : ($pct >= 80 ? 'bg-amber-400' : 'bg-lime-400');

            return $category;
        });
    }
}
