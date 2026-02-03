<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Seed categories from real product data
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $this->command->info('🏷️  Đang thêm categories từ database thật...');
        
        // Xóa categories cũ (nếu có)
        DB::table('category')->truncate();
        
        $categories = [
            // Parent categories (c_parent = 0)
            [
                'id' => 1,
                'c_name' => 'LAPTOP',
                'c_slug' => 'laptop',
                'c_parent' => 0,
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'c_name' => 'ĐIỆN THOẠI',
                'c_slug' => 'dien-thoai',
                'c_parent' => 0,
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'c_name' => 'GIA DỤNG',
                'c_slug' => 'gia-dung',
                'c_parent' => 0,
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'c_name' => 'TIVI',
                'c_slug' => 'tivi',
                'c_parent' => 0,
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'c_name' => 'PHỤ KIỆN',
                'c_slug' => 'phu-kien',
                'c_parent' => 0,
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Sub-categories LAPTOP (c_parent = 1)
            [
                'id' => 10,
                'c_name' => 'DELL',
                'c_slug' => 'dell',
                'c_parent' => 1, // LAPTOP
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'c_name' => 'ACER',
                'c_slug' => 'acer',
                'c_parent' => 1, // LAPTOP
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'c_name' => 'ASUS',
                'c_slug' => 'asus',
                'c_parent' => 1, // LAPTOP
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 13,
                'c_name' => 'HP',
                'c_slug' => 'hp',
                'c_parent' => 1, // LAPTOP
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 14,
                'c_name' => 'LENOVO',
                'c_slug' => 'lenovo',
                'c_parent' => 1, // LAPTOP
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 15,
                'c_name' => 'MSI',
                'c_slug' => 'msi',
                'c_parent' => 1, // LAPTOP
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Sub-categories ĐIỆN THOẠI (c_parent = 2)
            [
                'id' => 20,
                'c_name' => 'iPhone',
                'c_slug' => 'iphone',
                'c_parent' => 2, // ĐIỆN THOẠI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 21,
                'c_name' => 'Samsung',
                'c_slug' => 'samsung',
                'c_parent' => 2, // ĐIỆN THOẠI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 22,
                'c_name' => 'Xiaomi',
                'c_slug' => 'xiaomi',
                'c_parent' => 2, // ĐIỆN THOẠI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 23,
                'c_name' => 'OPPO',
                'c_slug' => 'oppo',
                'c_parent' => 2, // ĐIỆN THOẠI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 24,
                'c_name' => 'Vivo',
                'c_slug' => 'vivo',
                'c_parent' => 2, // ĐIỆN THOẠI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Sub-categories GIA DỤNG (c_parent = 3)
            [
                'id' => 30,
                'c_name' => 'Tủ Lạnh',
                'c_slug' => 'tu-lanh',
                'c_parent' => 3, // GIA DỤNG
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 31,
                'c_name' => 'Máy Giặt',
                'c_slug' => 'may-giat',
                'c_parent' => 3, // GIA DỤNG
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 32,
                'c_name' => 'Điều Hòa',
                'c_slug' => 'dieu-hoa',
                'c_parent' => 3, // GIA DỤNG
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 33,
                'c_name' => 'Nồi Cơm Điện',
                'c_slug' => 'noi-com-dien',
                'c_parent' => 3, // GIA DỤNG
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 34,
                'c_name' => 'Lò Vi Sóng',
                'c_slug' => 'lo-vi-song',
                'c_parent' => 3, // GIA DỤNG
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Sub-categories TIVI (c_parent = 4)
            [
                'id' => 40,
                'c_name' => 'Samsung TV',
                'c_slug' => 'samsung-tv',
                'c_parent' => 4, // TIVI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 41,
                'c_name' => 'LG TV',
                'c_slug' => 'lg-tv',
                'c_parent' => 4, // TIVI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 42,
                'c_name' => 'Sony TV',
                'c_slug' => 'sony-tv',
                'c_parent' => 4, // TIVI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 43,
                'c_name' => 'TCL TV',
                'c_slug' => 'tcl-tv',
                'c_parent' => 4, // TIVI
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // Sub-categories PHỤ KIỆN (c_parent = 5)
            [
                'id' => 50,
                'c_name' => 'Tai Nghe',
                'c_slug' => 'tai-nghe',
                'c_parent' => 5, // PHỤ KIỆN
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 51,
                'c_name' => 'Chuột',
                'c_slug' => 'chuot',
                'c_parent' => 5, // PHỤ KIỆN
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 52,
                'c_name' => 'Bàn Phím',
                'c_slug' => 'ban-phim',
                'c_parent' => 5, // PHỤ KIỆN
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 53,
                'c_name' => 'Loa',
                'c_slug' => 'loa',
                'c_parent' => 5, // PHỤ KIỆN
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 54,
                'c_name' => 'Sạc Dự Phòng',
                'c_slug' => 'sac-du-phong',
                'c_parent' => 5, // PHỤ KIỆN
                'c_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        
        // Insert categories
        foreach ($categories as $category) {
            DB::table('category')->insert($category);
        }
        
        $total = count($categories);
        $parentCount = count(array_filter($categories, fn($c) => $c['c_parent'] == 0));
        $childCount = $total - $parentCount;
        
        $this->command->info("\n✅ Đã thêm {$total} categories!");
        $this->command->info("   • {$parentCount} parent categories");
        $this->command->info("   • {$childCount} sub-categories");
        
        // Hiển thị cấu trúc menu
        $this->command->info("\n📋 Cấu trúc menu:");
        $parents = array_filter($categories, fn($c) => $c['c_parent'] == 0);
        foreach ($parents as $parent) {
            $this->command->line("  📁 {$parent['c_name']}");
            $children = array_filter($categories, fn($c) => $c['c_parent'] == $parent['id']);
            foreach ($children as $child) {
                $this->command->line("     └─ {$child['c_name']}");
            }
        }
    }
}
