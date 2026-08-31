<?php

namespace App\Queries\Budget;

use App\Models\Category;
use Carbon\Carbon;

// Returns the assigned budget, total spent, and rolled-over available balance
// for a single category in a given month. Used on the budget detail page (BudgetController@show).
class CategoryMonthDetailQuery
{
    public function __construct(
        private Category $category,
        private int $year,
        private int $month,
    ) {}

    public function handle(): array
    {
        $monthStart = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $cutoff = $monthStart->copy()->endOfMonth();

        $assigned = (float) ($this->category->budgets()
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->value('amount') ?? 0);

        $spent = (float) $this->category->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart->toDateString(), $cutoff->toDateString()])
            ->sum('amount');

        $cumulativeAssigned = (float) $this->category->budgets()
            ->where(function ($q) {
                $q->where('year', '<', $this->year)
                    ->orWhere(function ($q2) {
                        $q2->where('year', $this->year)->where('month', '<=', $this->month);
                    });
            })
            ->sum('amount');

        $cumulativeSpent = (float) $this->category->transactions()
            ->where('type', 'expense')
            ->where('date', '<=', $cutoff)
            ->sum('amount');

        return [
            'assigned'  => $assigned,
            'spent'     => $spent,
            'available' => $cumulativeAssigned - $cumulativeSpent,
        ];
    }
}
