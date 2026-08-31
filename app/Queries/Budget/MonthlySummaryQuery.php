<?php

namespace App\Queries\Budget;

use App\Models\Transaction;
use Carbon\Carbon;

// Returns total income and total expenses for a given month.
// Used on the home dashboard and budget page to show the month summary bar.
class MonthlySummaryQuery
{
    public function __construct(
        private int $year,
        private int $month,
    ) {}

    public function handle(): array
    {
        $start = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $totals = Transaction::query()
            ->selectRaw('type, SUM(amount) as total')
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('type')
            ->pluck('total', 'type');

        return [
            'income' => (float) ($totals['income'] ?? 0),
            'expenses' => (float) ($totals['expenses'] ?? 0),
        ];
    }
}
