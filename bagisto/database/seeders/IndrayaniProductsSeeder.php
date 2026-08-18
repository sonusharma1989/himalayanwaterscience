<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IndrayaniProductsSeeder extends Seeder
{
    /**
     * Category repository instance.
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Product repository instance.
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new seeder instance.
     *
     * @param  \Webkul\Category\Repositories\CategoryRepository  $categoryRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function run()
    {
        echo "Starting Indrayani Aquatech Setup with Product Images...\n";

        // Disable foreign key checks & collation triggers temporarily for clean category insertion
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        echo "Cleaning up old dummy products and categories...\n";
        DB::table('product_flat')->truncate();
        DB::table('product_categories')->truncate();
        DB::table('product_images')->truncate();
        DB::table('product_inventories')->truncate();
        DB::table('product_attribute_values')->truncate();
        DB::table('products')->truncate();
        
        // Remove non-root categories (Root category is 1)
        DB::table('category_translations')->where('category_id', '>', 1)->delete();
        DB::table('category_filterable_attributes')->where('category_id', '>', 1)->delete();
        DB::table('categories')->where('id', '>', 1)->delete();

        // Ensure INR currency exists and channel uses INR
        $inrCurrency = DB::table('currencies')->where('code', 'INR')->first();
        if (!$inrCurrency) {
            $inrCurrencyId = DB::table('currencies')->insertGetId([
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'decimal' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $inrCurrencyId = $inrCurrency->id;
            DB::table('currencies')->where('id', $inrCurrencyId)->update(['symbol' => '₹']);
        }

        // Set channel base currency to INR
        DB::table('channels')->where('code', 'default')->update([
            'base_currency_id' => $inrCurrencyId
        ]);

        $channelCurrencyExists = DB::table('channel_currencies')
            ->where('channel_id', 1)
            ->where('currency_id', $inrCurrencyId)
            ->first();

        if (!$channelCurrencyExists) {
            DB::table('channel_currencies')->insert([
                'channel_id' => 1,
                'currency_id' => $inrCurrencyId
            ]);
        }

        // Root category is 1 in Bagisto
        $rootCategoryId = 1;

        // High quality placeholder/water treatment images from Unsplash CDN
        $images = [
            'ro_plant' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80',
            'industrial_ro' => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=800&auto=format&fit=crop&q=80',
            'stp_etp' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&auto=format&fit=crop&q=80',
            'water_atm' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=800&auto=format&fit=crop&q=80',
            'water_chiller' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&auto=format&fit=crop&q=80',
            'membrane' => 'https://images.unsplash.com/photo-1617155093730-a8bf47be792d?w=800&auto=format&fit=crop&q=80',
            'parts' => 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?w=800&auto=format&fit=crop&q=80'
        ];

        $categories = [
            [
                'name' => 'Reverse Osmosis (RO) Plants',
                'slug' => 'ro-plants',
                'description' => 'Commercial and Industrial Reverse Osmosis (RO) Plants for pure water purification.',
                'subcategories' => [
                    [
                        'name' => 'Commercial RO Plants',
                        'slug' => 'commercial-ro-plants',
                        'description' => 'RO plants designed for schools, offices, hospitals, and commercial complexes.',
                        'products' => [
                            [
                                'sku' => 'IND-RO-250',
                                'name' => '250 LPH Commercial RO Plant (SS Skid)',
                                'price' => 45000,
                                'weight' => 80,
                                'image_url' => $images['ro_plant'],
                                'description' => 'High performance 250 Liters Per Hour Commercial RO Plant mounted on heavy-duty Stainless Steel skid frame with automatic control panel and CRI pump.',
                                'short_description' => '250 LPH SS Skid Commercial RO Water Plant.'
                            ],
                            [
                                'sku' => 'IND-RO-500',
                                'name' => '500 LPH Commercial RO Plant (FRP/SS)',
                                'price' => 65000,
                                'weight' => 120,
                                'image_url' => $images['ro_plant'],
                                'description' => 'Robust 500 LPH Commercial Reverse Osmosis Water Treatment Plant with high rejection membranes, sand & carbon filters.',
                                'short_description' => '500 LPH Commercial RO Plant with FRP Vessels.'
                            ],
                            [
                                'sku' => 'IND-RO-1000',
                                'name' => '1000 LPH Industrial RO Water Plant',
                                'price' => 115000,
                                'weight' => 200,
                                'image_url' => $images['industrial_ro'],
                                'description' => '1000 Liters Per Hour heavy duty Industrial Reverse Osmosis Plant equipped with TDS Controller, Multiport Valves, and Stainless Steel High Pressure Pump.',
                                'short_description' => '1000 LPH Industrial RO Plant for factories & institutions.'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Industrial RO Plants',
                        'slug' => 'industrial-ro-plants',
                        'description' => 'High capacity RO plants from 2000 LPH to 6000 LPH for heavy industrial manufacturing plants.',
                        'products' => [
                            [
                                'sku' => 'IND-RO-2000',
                                'name' => '2000 LPH Industrial RO Plant',
                                'price' => 195000,
                                'weight' => 350,
                                'image_url' => $images['industrial_ro'],
                                'description' => '2000 LPH Heavy Industrial Reverse Osmosis System with advanced monitoring instruments, flow meters, pressure gauges, and automated backwash.',
                                'short_description' => '2000 LPH Industrial RO System.'
                            ],
                            [
                                'sku' => 'IND-RO-5000',
                                'name' => '5000 LPH High Capacity RO Plant',
                                'price' => 420000,
                                'weight' => 750,
                                'image_url' => $images['industrial_ro'],
                                'description' => '5000 LPH High Capacity Industrial RO Plant with PLC automated control panel, Grundfos/CRI high pressure pumps and Dow Filmtec membranes.',
                                'short_description' => '5000 LPH High Capacity Industrial RO System.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Wastewater Treatment (STP / ETP)',
                'slug' => 'wastewater-treatment',
                'description' => 'Sewage Treatment Plants (STP) and Effluent Treatment Plants (ETP) for environmental compliance.',
                'subcategories' => [
                    [
                        'name' => 'Sewage Treatment Plants (STP)',
                        'slug' => 'sewage-treatment-plants-stp',
                        'description' => 'MBBR and SBR technology based Sewage Treatment Plants for residential societies and commercial buildings.',
                        'products' => [
                            [
                                'sku' => 'IND-STP-MBBR-10K',
                                'name' => '10 KLD MBBR Sewage Treatment Plant',
                                'price' => 280000,
                                'weight' => 500,
                                'image_url' => $images['stp_etp'],
                                'description' => '10 KLD Moving Bed Biofilm Reactor (MBBR) STP Plant for efficient biological treatment of sewage water.',
                                'short_description' => '10 KLD MBBR Sewage Treatment Plant.'
                            ],
                            [
                                'sku' => 'IND-STP-50K',
                                'name' => '50 KLD Packaged Sewage Treatment Plant',
                                'price' => 650000,
                                'weight' => 1200,
                                'image_url' => $images['stp_etp'],
                                'description' => '50 KLD Packaged STP Plant suitable for residential apartments, hotels, and hospitals with low power consumption.',
                                'short_description' => '50 KLD Packaged STP Plant.'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Effluent Treatment Plants (ETP)',
                        'slug' => 'effluent-treatment-plants-etp',
                        'description' => 'Industrial ETP systems for textile, chemical, pharmaceutical, and food industries.',
                        'products' => [
                            [
                                'sku' => 'IND-ETP-25K',
                                'name' => '25 KLD Industrial Effluent Treatment Plant',
                                'price' => 480000,
                                'weight' => 900,
                                'image_url' => $images['stp_etp'],
                                'description' => '25 KLD Industrial ETP with chemical dosing tanks, flash mixer, clarifier, and pressure sand filter.',
                                'short_description' => '25 KLD Industrial ETP Plant.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Water ATM & Dispensing',
                'slug' => 'water-atm-dispensing',
                'description' => 'Smart Water Vending Machines and Water ATMs with Coin, Card, and UPI payment systems.',
                'subcategories' => [
                    [
                        'name' => 'Coin & Card Water ATMs',
                        'slug' => 'coin-card-water-atms',
                        'description' => 'Automated Water Vending Machines equipped with multi-coin and RFID card readers.',
                        'products' => [
                            [
                                'sku' => 'IND-ATM-COIN-500',
                                'name' => '500 LPH Automatic Coin & Card Water ATM',
                                'price' => 135000,
                                'weight' => 150,
                                'image_url' => $images['water_atm'],
                                'description' => '500 LPH RO Water ATM with Stainless Steel cabinet, GSM cloud reporting, Coin and Smart Card dispenser.',
                                'short_description' => '500 LPH RO Water ATM Machine.'
                            ],
                            [
                                'sku' => 'IND-ATM-SOLAR-250',
                                'name' => 'Solar Powered Water ATM Booth 250 LPH',
                                'price' => 185000,
                                'weight' => 220,
                                'image_url' => $images['water_atm'],
                                'description' => 'Eco-friendly 250 LPH Solar Powered Water ATM with battery backup and all-weather SS kiosk.',
                                'short_description' => '250 LPH Solar Powered Water ATM Kiosk.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Water Chillers & Coolers',
                'slug' => 'water-chillers-coolers',
                'description' => 'Industrial Water Chillers and Commercial Stainless Steel Water Coolers.',
                'subcategories' => [
                    [
                        'name' => 'Industrial Water Chillers',
                        'slug' => 'industrial-water-chillers',
                        'description' => 'Air-cooled and Water-cooled Industrial Chillers for process cooling and drinking water.',
                        'products' => [
                            [
                                'sku' => 'IND-CHILL-2TR',
                                'name' => '2 TR Air Cooled Industrial Water Chiller',
                                'price' => 95000,
                                'weight' => 110,
                                'image_url' => $images['water_chiller'],
                                'description' => '2 Ton Air Cooled Water Chiller with digital temperature controller, Emerson Copeland compressor, and insulated SS tank.',
                                'short_description' => '2 TR Air Cooled Industrial Chiller.'
                            ],
                            [
                                'sku' => 'IND-CHILL-5TR',
                                'name' => '5 TR Industrial Water Chiller Plant',
                                'price' => 175000,
                                'weight' => 240,
                                'image_url' => $images['water_chiller'],
                                'description' => '5 Ton Heavy Duty Air Cooled Process Water Chiller for commercial applications.',
                                'short_description' => '5 TR Heavy Duty Industrial Water Chiller.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Components & Spare Parts',
                'slug' => 'components-spare-parts',
                'description' => 'RO Membranes, Multiport Valves, Filter Media, FRP Tanks, and UV Systems.',
                'subcategories' => [
                    [
                        'name' => 'RO & UF Membranes',
                        'slug' => 'ro-uf-membranes',
                        'description' => 'Industrial 4040 and 8040 RO membranes for water purification plants.',
                        'products' => [
                            [
                                'sku' => 'IND-MEM-4040',
                                'name' => 'Industrial 4040 RO Membrane (High TDS Rejection)',
                                'price' => 6500,
                                'weight' => 4,
                                'image_url' => $images['membrane'],
                                'description' => 'High performance 4040 Industrial RO Membrane with 99.5% salt rejection for brackish water.',
                                'short_description' => '4040 Industrial RO Membrane.'
                            ],
                            [
                                'sku' => 'IND-MEM-8040',
                                'name' => 'Industrial 8040 RO Membrane',
                                'price' => 18500,
                                'weight' => 14,
                                'image_url' => $images['membrane'],
                                'description' => 'High capacity 8040 RO Membrane element for large scale industrial RO water systems.',
                                'short_description' => '8040 High Capacity RO Membrane.'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Multiport Valves & Media',
                        'slug' => 'multiport-valves-media',
                        'description' => 'Top/Side mounted Multiport Valves, Activated Carbon, and Quartz Sand Filter Media.',
                        'products' => [
                            [
                                'sku' => 'IND-MPV-25',
                                'name' => '25NB Top Mounted Multiport Valve (Filter/Softener)',
                                'price' => 2400,
                                'weight' => 2,
                                'image_url' => $images['parts'],
                                'description' => '25NB Top Mounted Multiport Valve for Sand Filters and Water Softeners.',
                                'short_description' => '25NB Multiport Valve for Filters.'
                            ],
                            [
                                'sku' => 'IND-CARBON-IV900',
                                'name' => 'Activated Carbon IV 900 (50 Kg Bag)',
                                'price' => 4200,
                                'weight' => 50,
                                'image_url' => $images['parts'],
                                'description' => 'High Iodine Value (IV 900) Coconut Shell Activated Carbon for odor, color, and organic removal in water filters.',
                                'short_description' => 'Activated Carbon IV 900 (50kg).'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($categories as $catData) {
            $mainCatId = $this->createCategory([
                'name' => $catData['name'],
                'slug' => $catData['slug'],
                'description' => $catData['description'],
                'parent_id' => $rootCategoryId
            ]);

            echo "Created Category: {$catData['name']}\n";

            if (isset($catData['subcategories'])) {
                foreach ($catData['subcategories'] as $subCatData) {
                    $subCatId = $this->createCategory([
                        'name' => $subCatData['name'],
                        'slug' => $subCatData['slug'],
                        'description' => $subCatData['description'],
                        'parent_id' => $mainCatId
                    ]);

                    echo "  -> Created Sub-category: {$subCatData['name']}\n";

                    if (isset($subCatData['products'])) {
                        foreach ($subCatData['products'] as $prodData) {
                            $this->createSimpleProduct($prodData, [$mainCatId, $subCatId]);
                            echo "     * Created Product with Image: {$prodData['name']}\n";
                        }
                    }
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Indrayani Aquatech Categories & Products with Images successfully seeded!\n";
    }

    private function createCategory($data)
    {
        $existingTranslation = DB::table('category_translations')->where('slug', $data['slug'])->first();
        if ($existingTranslation) {
            return $existingTranslation->category_id;
        }

        $parentId = $data['parent_id'];

        $categoryId = DB::table('categories')->insertGetId([
            'position' => 1,
            'image' => null,
            'status' => 1,
            'display_mode' => 'products_and_description',
            'parent_id' => $parentId,
            '_lft' => 1,
            '_rgt' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $urlPath = $data['slug'];
        if ($parentId && $parentId > 1) {
            $parentTranslation = DB::table('category_translations')->where('category_id', $parentId)->first();
            if ($parentTranslation && $parentTranslation->url_path) {
                $urlPath = $parentTranslation->url_path . '/' . $data['slug'];
            }
        }

        DB::table('category_translations')->insert([
            'category_id' => $categoryId,
            'locale' => 'en',
            'name' => $data['name'],
            'slug' => $data['slug'],
            'url_path' => $urlPath,
            'description' => $data['description'],
            'meta_title' => $data['name'],
            'meta_keywords' => $data['name'],
            'meta_description' => $data['description'],
        ]);

        // Attach default filterable attributes (Price=11, Brand/Color/Category=1,2,3,12) to category for sidebar filter
        $filterableAttributes = [11, 12, 1, 2, 3];
        foreach ($filterableAttributes as $attrId) {
            DB::table('category_filterable_attributes')->insertOrIgnore([
                'category_id' => $categoryId,
                'attribute_id' => $attrId
            ]);
        }

        // Attach Category to Velocity Header Menu if table exists
        if (Schema::hasTable('velocity_category_fields')) {
            $existingMenuItem = DB::table('velocity_category_fields')->where('category_id', $categoryId)->first();
            if (!$existingMenuItem) {
                DB::table('velocity_category_fields')->insert([
                    'category_id' => $categoryId,
                    'status' => 1,
                    'icon_class' => 'la la-tint',
                    'tooltip' => $data['name']
                ]);
            }
        }

        return $categoryId;
    }

    private function createSimpleProduct($data, $categoryIds)
    {
        $existing = DB::table('products')->where('sku', $data['sku'])->first();
        if ($existing) {
            $productId = $existing->id;
        } else {
            $urlKey = Str::slug($data['name']) . '-' . rand(100, 999);

            $productId = DB::table('products')->insertGetId([
                'type' => 'simple',
                'attribute_family_id' => 1,
                'sku' => $data['sku'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('product_flat')->insert([
                'product_id' => $productId,
                'sku' => $data['sku'],
                'name' => $data['name'],
                'url_key' => $urlKey,
                'new' => 1,
                'featured' => 1,
                'status' => 1,
                'visible_individually' => 1,
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'price' => $data['price'],
                'min_price' => $data['price'],
                'max_price' => $data['price'],
                'weight' => $data['weight'],
                'channel' => 'default',
                'locale' => 'en',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Safely insert attribute values without duplicates using updateOrInsert
            $attributesToSet = [
                ['attribute_id' => 1, 'text_value' => $data['sku']],
                ['attribute_id' => 2, 'text_value' => $data['name']],
                ['attribute_id' => 3, 'text_value' => $urlKey],
                ['attribute_id' => 7, 'boolean_value' => 1],
                ['attribute_id' => 8, 'boolean_value' => 1],
                ['attribute_id' => 9, 'text_value' => $data['short_description']],
                ['attribute_id' => 10, 'text_value' => $data['description']],
                ['attribute_id' => 11, 'float_value' => $data['price']],
                ['attribute_id' => 12, 'float_value' => $data['weight']],
            ];

            foreach ($attributesToSet as $attr) {
                DB::table('product_attribute_values')->updateOrInsert(
                    [
                        'product_id' => $productId,
                        'attribute_id' => $attr['attribute_id'],
                        'channel' => 'default',
                        'locale' => 'en'
                    ],
                    $attr
                );
            }

            foreach ($categoryIds as $catId) {
                DB::table('product_categories')->insertOrIgnore([
                    'product_id' => $productId,
                    'category_id' => $catId
                ]);
            }

            DB::table('product_inventories')->insertOrIgnore([
                'qty' => 50,
                'product_id' => $productId,
                'inventory_source_id' => 1,
                'vendor_id' => 0
            ]);
        }

        // Attach Image to Product
        if (isset($data['image_url'])) {
            $this->downloadAndAttachImage($productId, $data['image_url'], $data['sku']);
        }
    }

    private function downloadAndAttachImage($productId, $imageUrl, $sku)
    {
        try {
            $imageContents = @file_get_contents($imageUrl);
            if ($imageContents) {
                $dirPath = 'product/' . $productId;
                $filename = $dirPath . '/' . Str::slug($sku) . '.jpg';

                // Save to storage/app/public/product/{id}
                Storage::disk('public')->put($filename, $imageContents);

                // Insert into product_images table if not exists
                $imageExists = DB::table('product_images')->where('product_id', $productId)->first();
                if (!$imageExists) {
                    DB::table('product_images')->insert([
                        'product_id' => $productId,
                        'path' => $filename,
                        'type' => 'images'
                    ]);
                }

                // Update product_flat base image so Velocity home page picks it up immediately
                DB::table('product_flat')
                    ->where('product_id', $productId)
                    ->update([
                        'base_image' => json_encode([
                            'small_image_url' => Storage::url($filename),
                            'medium_image_url' => Storage::url($filename),
                            'large_image_url' => Storage::url($filename),
                            'original_image_url' => Storage::url($filename),
                        ])
                    ]);
            }
        } catch (\Exception $e) {
            // Ignore download error if offline
        }
    }
}
