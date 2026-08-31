<?php

namespace App\Queries\Stats;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Returns income and expenses totals for the last N months (default 6), ordered oldest → newest.
// Used to render the trend line/bar chart on the stats page.
// Labels are translated month abbreviations (e.g. 'Gen', 'Feb') based on the active locale.
class MonthlyTrendQuery
{
    public function __construct(private int $months = 6) {}

    public function handle(): array
    {
        $start = Carbon::now()->subMonths($this->months - 1)->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totals = Transaction::query()
            ->selectRaw('type, EXTRACT(YEAR FROM date) as year, EXTRACT(MONTH FROM date) as month, SUM(amount) as total')
            ->whereIn('type', ['income', 'expense'])
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('type', DB::raw('EXTRACT(YEAR FROM date)'), DB::raw('EXTRACT(MONTH FROM date)'))
            ->get()
            ->groupBy(fn ($row) => ((int) $row->year).'-'.((int) $row->month));

        $result = [];

        for ($i = $this->months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->year.'-'.$date->month;
            $monthRows = $totals->get($key, collect());

            $result[] = [
                'label' => $date->translatedFormat('M'),
                'income' => (float) ($monthRows->firstWhere('type', 'income')?->total ?? 0),
                'expenses' => (float) ($monthRows->firstWhere('type', 'expense')?->total ?? 0),
            ];
        }

        return $result;
    }
}
