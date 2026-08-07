<?php

namespace App\Queries\Budget;

use App\Models\Category;
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
        $cutoff = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();

        return Category::where('is_goal', false)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) use ($cutoff) {
                $assigned = (float) ($category->budgets()
                    ->where('year', $this->year)
                    ->where('month', $this->month)
                    ->value('amount') ?? 0);

                $spent = (float) $category->transactions()
                    ->where('type', 'expense')
                    ->whereYear('date', $this->year)
                    ->whereMonth('date', $this->month)
                    ->sum('amount');

                $cumulativeAssigned = (float) $category->budgets()
                    ->where(function ($q) {
                        $q->where('year', '<', $this->year)
                            ->orWhere(function ($q2) {
                                $q2->where('year', $this->year)->where('month', '<=', $this->month);
                            });
                    })
                    ->sum('amount');

                $cumulativeSpent = (float) $category->transactions()
                    ->where('type', 'expense')
                    ->where('date', '<=', $cutoff)
                    ->sum('amount');

                $available   = $cumulativeAssigned - $cumulativeSpent;
                $totalBudget = $assigned;
                $pct         = $totalBudget > 0 ? min(100, round($spent / $totalBudget * 100)) : 0;

                $category->assigned         = $assigned;
                $category->spent            = $spent;
                $category->available        = $available;
                $category->total_budget     = $totalBudget;
                $category->pct              = $pct;
                // bar_color variants for desktop (500) and mobile (400/amber/lime) Tailwind classes
                $category->bar_color        = $available < 0 ? 'bg-red-500' : ($pct >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                $category->bar_color_mobile = $available < 0 ? 'bg-red-400' : ($pct >= 80 ? 'bg-amber-400' : 'bg-lime-400');

                return $category;
            });
    }
}
