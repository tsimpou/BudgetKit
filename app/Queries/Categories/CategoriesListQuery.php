<?php

namespace App\Queries\Categories;

use App\Models\Category;
use App\Support\PageCache;
use Illuminate\Support\Collection;

// Returns all non-goal categories ordered by sort_order.
// Goals are stored as categories with is_goal=true and are excluded here.
class CategoriesListQuery
{
    public function handle(): Collection
    {
        return PageCache::remember('categories_list', 300, function () {
            return Category::where('is_goal', false)
                ->orderBy('sort_order')
                ->get();
        });
    }
}
