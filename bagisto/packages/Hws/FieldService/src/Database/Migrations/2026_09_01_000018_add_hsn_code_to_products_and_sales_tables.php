<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Check if hsn_code attribute exists in attributes table, create if not
        $attribute = DB::table('attributes')->where('code', 'hsn_code')->first();
        if (!$attribute) {
            $attributeId = DB::table('attributes')->insertGetId([
                'code'                => 'hsn_code',
                'admin_name'          => 'HSN Code',
                'type'                => 'text',
                'validation'          => null,
                'position'            => 3,
                'is_required'         => 0,
                'is_unique'           => 0,
                'value_per_locale'    => 0,
                'value_per_channel'   => 0,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 1,
                'use_in_flat'         => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Add translation
            DB::table('attribute_translations')->insert([
                'locale'       => 'en',
                'name'         => 'HSN Code',
                'attribute_id' => $attributeId,
            ]);

            // Map to default attribute family -> General group
            $family = DB::table('attribute_families')->where('code', 'default')->first();
            if ($family) {
                $group = DB::table('attribute_groups')
                    ->where('attribute_family_id', $family->id)
                    ->where('name', 'General')
                    ->first();

                if ($group) {
                    DB::table('attribute_group_mappings')->insert([
                        'attribute_id'       => $attributeId,
                        'attribute_group_id' => $group->id,
                        'position'           => 3,
                    ]);
                }
            }
        }

        // 2. Add hsn_code column to product_flat if missing
        if (Schema::hasTable('product_flat') && !Schema::hasColumn('product_flat', 'hsn_code')) {
            Schema::table('product_flat', function (Blueprint $table) {
                $table->string('hsn_code')->nullable()->after('product_number');
            });
        }

        // 3. Add hsn_code to order_items if missing
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'hsn_code')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('hsn_code')->nullable()->after('sku');
            });
        }

        // 4. Add hsn_code to invoice_items if missing
        if (Schema::hasTable('invoice_items') && !Schema::hasColumn('invoice_items', 'hsn_code')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->string('hsn_code')->nullable()->after('sku');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'hsn_code')) {
            Schema::table('invoice_items', fn (Blueprint $table) => $table->dropColumn('hsn_code'));
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'hsn_code')) {
            Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('hsn_code'));
        }

        if (Schema::hasTable('product_flat') && Schema::hasColumn('product_flat', 'hsn_code')) {
            Schema::table('product_flat', fn (Blueprint $table) => $table->dropColumn('hsn_code'));
        }
    }
};
