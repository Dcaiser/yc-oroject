<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            if (Schema::hasColumn('products', 'category_id') && Schema::hasColumn('products', 'stock_quantity') && Schema::hasColumn('products', 'name')) {
                $this->addIndexIfMissing('products', 'products_category_stock_name_idx', function (Blueprint $table) {
                    $table->index(['category_id', 'stock_quantity', 'name'], 'products_category_stock_name_idx');
                });
            }

            if (Schema::hasColumn('products', 'stock_quantity')) {
                $this->addIndexIfMissing('products', 'products_stock_quantity_idx', function (Blueprint $table) {
                    $table->index('stock_quantity', 'products_stock_quantity_idx');
                });
            }
        }

        if (Schema::hasTable('prices') && Schema::hasColumn('prices', 'product_id') && Schema::hasColumn('prices', 'customer_type')) {
            $this->addIndexIfMissing('prices', 'prices_product_customer_type_idx', function (Blueprint $table) {
                $table->index(['product_id', 'customer_type'], 'prices_product_customer_type_idx');
            });
        }

        if (Schema::hasTable('pos_transactions')) {
            if (Schema::hasColumn('pos_transactions', 'status') && Schema::hasColumn('pos_transactions', 'balance_due')) {
                $this->addIndexIfMissing('pos_transactions', 'pos_transactions_status_balance_due_idx', function (Blueprint $table) {
                    $table->index(['status', 'balance_due'], 'pos_transactions_status_balance_due_idx');
                });
            }

            if (Schema::hasColumn('pos_transactions', 'balance_due')) {
                $this->addIndexIfMissing('pos_transactions', 'pos_transactions_balance_due_idx', function (Blueprint $table) {
                    $table->index('balance_due', 'pos_transactions_balance_due_idx');
                });
            }

            if (Schema::hasColumn('pos_transactions', 'created_at') && Schema::hasColumn('pos_transactions', 'status')) {
                $this->addIndexIfMissing('pos_transactions', 'pos_transactions_created_status_idx', function (Blueprint $table) {
                    $table->index(['created_at', 'status'], 'pos_transactions_created_status_idx');
                });
            }
        }

        if (Schema::hasTable('activities')) {
            if (Schema::hasColumn('activities', 'created_at')) {
                $this->addIndexIfMissing('activities', 'activities_created_at_idx', function (Blueprint $table) {
                    $table->index('created_at', 'activities_created_at_idx');
                });
            }

            if (Schema::hasColumn('activities', 'created_at') && Schema::hasColumn('activities', 'user')) {
                $this->addIndexIfMissing('activities', 'activities_created_user_idx', function (Blueprint $table) {
                    $table->index(['created_at', 'user'], 'activities_created_user_idx');
                });
            }
        }

        if (Schema::hasTable('stock_in') && Schema::hasColumn('stock_in', 'created_at')) {
            $this->addIndexIfMissing('stock_in', 'stock_in_created_at_idx', function (Blueprint $table) {
                $table->index('created_at', 'stock_in_created_at_idx');
            });
        }

        if (Schema::hasTable('stock_out') && Schema::hasColumn('stock_out', 'created_at')) {
            $this->addIndexIfMissing('stock_out', 'stock_out_created_at_idx', function (Blueprint $table) {
                $table->index('created_at', 'stock_out_created_at_idx');
            });
        }
    }

    public function down(): void
    {
        $this->dropIndexIfExists('stock_out', 'stock_out_created_at_idx');
        $this->dropIndexIfExists('stock_in', 'stock_in_created_at_idx');
        $this->dropIndexIfExists('activities', 'activities_created_user_idx');
        $this->dropIndexIfExists('activities', 'activities_created_at_idx');
        $this->dropIndexIfExists('pos_transactions', 'pos_transactions_created_status_idx');
        $this->dropIndexIfExists('pos_transactions', 'pos_transactions_balance_due_idx');
        $this->dropIndexIfExists('pos_transactions', 'pos_transactions_status_balance_due_idx');
        $this->dropIndexIfExists('prices', 'prices_product_customer_type_idx');
        $this->dropIndexIfExists('products', 'products_stock_quantity_idx');
        $this->dropIndexIfExists('products', 'products_category_stock_name_idx');
    }

    private function addIndexIfMissing(string $tableName, string $indexName, callable $callback): void
    {
        if (!Schema::hasTable($tableName) || $this->hasIndex($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, $callback);
    }

    private function dropIndexIfExists(string $tableName, string $indexName): void
    {
        if (!Schema::hasTable($tableName) || !$this->hasIndex($tableName, $indexName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    private function hasIndex(string $tableName, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $tableName)
            ->where('index_name', $indexName)
            ->exists();
    }
};
