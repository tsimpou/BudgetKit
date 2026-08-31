<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Queries\Categories\CategoriesListQuery;
use App\Services\CategoryService;
use App\Support\PageCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    // List all non-goal categories ordered by sort_order.
    public function index(): View
    {
        return $this->mobileView('categories.index', [
            'categories' => (new CategoriesListQuery)->handle(),
        ]);
    }

    // Show the form to create a new category.
    public function create(): View
    {
        return $this->mobileView('categories.create', [
            'categoryColors' => config('budget.category_colors'),
        ]);
    }

    // Persist a new category, appending it at the end of the sort order.
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data               = $request->validated();
        $data['sort_order'] = Category::where('is_goal', false)->max('sort_order') + 1;
        $data['is_goal']    = false;

        Category::create($data);

        PageCache::forget('categories_list');

        session()->flash('success', __('notifications.toast_saved'));

        return redirect()->route('categories.index');
    }

    // Show the edit form for an existing category.
    public function edit(Category $category): View
    {
        return $this->mobileView('categories.edit', [
            'category'       => $category,
            'categoryColors' => config('budget.category_colors'),
        ]);
    }

    // Update a category's data and clear its translation key since it's now user-defined.
    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update(array_merge($request->validated(), ['translation_key' => null]));

        PageCache::forget('categories_list');

        session()->flash('success', __('notifications.toast_saved'));

        return redirect()->route('categories.index');
    }

    // Delete a category; blocks deletion if it still has transactions or budgets attached.
    public function destroy(Category $category): RedirectResponse
    {
        $deleted = $this->categoryService->delete($category);

        if (! $deleted) {
            return redirect()->route('categories.index')
                ->with('error', __('categories.category_has_data'));
        }

        PageCache::forget('categories_list');

        session()->flash('success', __('notifications.toast_deleted'));

        return redirect()->route('categories.index')
            ->with('success', __('notifications.toast_deleted'));
    }
}
