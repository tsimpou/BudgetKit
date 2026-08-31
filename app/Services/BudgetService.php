<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Support\PageCache;
use Carbon\Carbon;

// Handles write operations related to budget and transactions.
// Read/aggregation logic lives in the Queries layer.
class BudgetService
{
    // Creates a new expense transaction from validated form data.
    public function storeExpense(array $data): Transaction
    {
        $transaction = Transaction::create([
            'type'        => 'expense',
            'amount'      => $data['amount'],
            'category_id' => $data['category_id'],
            'date'        => $data['date'],
            'note'        => $data['note'] ?? null,
        ]);

        $this->invalidateForDate($data['date']);

        return $transaction;
    }

    // Creates a new income transaction. Income has no category_id.
    public function storeIncome(array $data): Transaction
    {
        $transaction = Transaction::create([
            'type'   => 'income',
            'amount' => $data['amount'],
            'date'   => $data['date'],
            'note'   => $data['note'] ?? null,
        ]);

        $this->invalidateForDate($data['date']);

        return $transaction;
    }

    // Sets (or overwrites) the budget limit for a category in a given month.
    // Uses updateOrCreate so calling it twice is safe (idempotent).
    public function assignBudget(int $categoryId, int $year, int $month, float $amount): void
    {
        Budget::updateOrCreate(
            ['category_id' => $categoryId, 'year' => $year, 'month' => $month],
            ['amount' => $amount]
        );

        PageCache::forgetMonth($year, $month);
    }

    // Copies all non-zero budget entries from the previous month into the given month.
    // Skips categories that already have a budget entry for the target month.
    // Returns the number of entries actually created.
    public function copyBudgetFromPreviousMonth(int $year, int $month): int
    {
        $prev = Carbon::createFromDate($year, $month, 1)->subMonth();

        $prevBudgets = Budget::where('year', $prev->year)
            ->where('month', $prev->month)
            ->where('amount', '>', 0)
            ->get();

        $copied = 0;
        foreach ($prevBudgets as $budget) {
            $created = Budget::firstOrCreate(
                ['category_id' => $budget->category_id, 'year' => $year, 'month' => $month],
                ['amount' => $budget->amount]
            );
            if ($created->wasRecentlyCreated) {
                $copied++;
            }
        }

        PageCache::forgetMonth($year, $month);

        return $copied;
    }

    private function invalidateForDate(string $date): void
    {
        $parsed = Carbon::parse($date);
        PageCache::forgetMonth($parsed->year, $parsed->month);
    }
}
