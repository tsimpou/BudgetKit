<?php

namespace App\Queries\Goals;

use App\Models\Category;
use Illuminate\Support\Collection;

// Returns all goal categories enriched with progress data.
// Goals reuse the Category model with is_goal=true; savings are stored as budget entries.
// Each goal gets:
//   - saved:        total amount saved across all budget entries for this goal
//   - progress_pct: percentage toward target_amount, capped at 100
class GoalsProgressQuery
{
    public function handle(): Collection
    {
        return Category::where('is_goal', true)
            ->withSum('budgets', 'amount')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($goal) {
                $saved = (float) ($goal->budgets_sum_amount ?? 0);
                $goal->saved = $saved;
                $goal->progress_pct = $goal->target_amount > 0
                    ? min(100, round(($saved / $goal->target_amount) * 100))
                    : 0;

                return $goal;
            });
    }
}
