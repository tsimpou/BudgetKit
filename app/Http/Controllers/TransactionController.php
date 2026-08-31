<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesMonth;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Queries\Budget\MonthlySummaryQuery;
use App\Queries\Categories\CategoriesListQuery;
use App\Queries\Transactions\BudgetExceededQuery;
use App\Queries\Transactions\MonthlyTransactionsQuery;
use App\Services\BudgetService;
use App\Services\ReceiptService;
use App\Support\PageCache;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransactionController extends Controller
{
    use ResolvesMonth;

    public function __construct(
        private BudgetService $budgetService,
        private ReceiptService $receiptService,
    ) {}

    // List transactions for the selected month, optionally filtered by type and/or category.
    public function index(): View
    {
        $date       = $this->getSelectedMonth();
        $type       = request()->string('type', '')->value() ?: null;
        $categoryId = request()->integer('category_id') ?: null;

        $filterParams = array_filter([
            'type'        => $type,
            'category_id' => $categoryId,
        ]);

        $summary = (new MonthlySummaryQuery($date->year, $date->month))->handle();

        return $this->mobileView('transactions.index', [
            'transactions'     => (new MonthlyTransactionsQuery($date->year, $date->month, $type, $categoryId))->handle(),
            'categories'       => (new CategoriesListQuery)->handle(),
            'activeType'       => $type,
            'activeCategoryId' => $categoryId,
            'monthName'        => $date->translatedFormat('F Y'),
            'year'             => $date->year,
            'month'            => $date->month,
            'prevUrl'          => route('transactions.index', array_merge($this->monthParams($date, -1), $filterParams)),
            'nextUrl'          => route('transactions.index', array_merge($this->monthParams($date, +1), $filterParams)),
            'isCurrentMonth'   => $date->isSameMonth(Carbon::now()),
            'totalIncome'      => $summary['income'],
            'totalExpenses'    => $summary['expenses'],
        ]);
    }

    // Show the form to record a new expense transaction.
    public function create(): View
    {
        return $this->mobileView('transactions.create', [
            'categories' => Category::where('is_goal', false)->orderBy('sort_order')->get(),
            'today'      => Carbon::today()->format('Y-m-d'),
        ]);
    }

    // Persist a new expense; flashes a budget-exceeded warning if the category limit is breached.
    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $transaction = $this->budgetService->storeExpense($request->validated());

        if ($request->hasFile('receipt')) {
            $this->receiptService->attach($transaction, $request->file('receipt'));
        }

        $exceeded = (new BudgetExceededQuery($transaction))->handle();
        if ($exceeded) {
            session()->flash('budget_exceeded', $exceeded);
        }

        return redirect()->route('home');
    }

    // Show the edit form for an existing transaction (expense or income).
    public function edit(Transaction $transaction): View
    {
        return $this->mobileView('transactions.edit', [
            'transaction' => $transaction,
            'categories'  => (new CategoriesListQuery)->handle(),
        ]);
    }

    // Update a transaction; category_id is only applied for expenses, not for income.
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validated();

        if ($transaction->type === 'income') {
            unset($validated['category_id']);
        }

        unset($validated['receipt'], $validated['remove_receipt']);

        $transaction->update($validated);

        PageCache::forgetMonth(
            Carbon::parse($validated['date'])->year,
            Carbon::parse($validated['date'])->month
        );

        if ($transaction->wasChanged('date')) {
            PageCache::forgetMonth($transaction->getOriginal('date')->year, $transaction->getOriginal('date')->month);
        }

        if ($request->boolean('remove_receipt')) {
            $this->receiptService->delete($transaction);
        } elseif ($request->hasFile('receipt')) {
            $this->receiptService->attach($transaction, $request->file('receipt'));
        }

        session()->flash('success', __('notifications.toast_saved'));

        $date = Carbon::parse($validated['date']);

        return redirect()->route('transactions.index', ['year' => $date->year, 'month' => $date->month]);
    }

    // Preview the receipt inline in the browser.
    public function receiptPreview(Transaction $transaction): BinaryFileResponse
    {
        return $this->receiptService->preview($transaction);
    }

    // Force-download the receipt file.
    public function receiptDownload(Transaction $transaction): BinaryFileResponse
    {
        return $this->receiptService->download($transaction);
    }

    // Delete a transaction and redirect back to the same month view.
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $year  = request()->integer('year',  Carbon::now()->year);
        $month = request()->integer('month', Carbon::now()->month);

        $transactionDate = $transaction->date;
        $transaction->delete();

        PageCache::forgetMonth(
            Carbon::parse($transactionDate)->year,
            Carbon::parse($transactionDate)->month
        );

        session()->flash('success', __('notifications.toast_deleted'));

        return redirect()->route('transactions.index', ['year' => $year, 'month' => $month]);
    }

}
