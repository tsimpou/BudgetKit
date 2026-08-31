<?php

namespace App\Queries\Budget;

use App\Support\PageCache;
use Illuminate\Support\Facades\DB;

// Returns how much money is still unassigned to any budget category.
// Formula: total income (all time) - total assigned across all budgets.
// Used on the budget page to show how much the user can still allocate.
class ReadyToAssignQuery
{
    public function handle(): float
    {
        return PageCache::remember('rta', 120, function () {
            $result = DB::selectOne('
                SELECT
                    (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE type = ?) -
                    (SELECT COALESCE(SUM(amount), 0) FROM budgets) AS ready
            ', ['income']);

            return (float) ($result->ready ?? 0);
        });
    }
}
