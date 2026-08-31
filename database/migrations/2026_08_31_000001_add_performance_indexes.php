<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('date');
            $table->index(['type', 'date']);
            $table->index(['category_id', 'type', 'date']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->index(['category_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['type', 'date']);
            $table->dropIndex(['category_id', 'type', 'date']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'year', 'month']);
        });
    }
};
