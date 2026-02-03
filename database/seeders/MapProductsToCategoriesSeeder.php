<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapProductsToCategoriesSeeder extends Seeder
{
    /**
     * Map existing products to correct categories based on product names
     */
    public function run(): void
    {
        $this->command->info('🔄 Đang map sản phẩm với categories...');
        
        // Mapping rules: Từ khóa trong tên sản phẩm -> Category ID
        $mappings = [
            // LAPTOP brands (c_parent = 1)
            'DELL' => 10,
            'Dell' => 10,
            'dell' => 10,
            
            'ACER' => 11,
            'Acer' => 11,
            'acer' => 11,
            'Aspire' => 11,
            
            'ASUS' => 12,
            'Asus' => 12,
            'asus' => 12,
            'ROG' => 12,
            'Vivobook' => 12,
            'Zenbook' => 12,
            
            'HP Pavilion' => 13,
            'HP Envy' => 13,
            'EliteBook' => 13,
            'HP EliteBook' => 13,
            
            'LENOVO' => 14,
            'Lenovo' => 14,
            'lenovo' => 14,
            'ThinkPad' => 14,
            'IdeaPad' => 14,
            
            'MSI' => 15,
            'Msi' => 15,
            'msi' => 15,
            
            // ĐIỆN THOẠI brands (c_parent = 2)
            'iPhone' => 20,
            'iphone' => 20,
            'IPHONE' => 20,
            
            'Samsung Galaxy' => 21,
            'Samsung' => 21,
            'Galaxy' => 21,
            
            'Xiaomi' => 22,
            'xiaomi' => 22,
            'Redmi' => 22,
            'Poco' => 22,
            
            'OPPO' => 23,
            'Oppo' => 23,
            'oppo' => 23,
            
            'Vivo' => 24,
            'vivo' => 24,
            'VIVO' => 24,
            
            // GIA DỤNG (c_parent = 3)
            'Tủ lạnh' => 30,
            'Tủ Lạnh' => 30,
            'TỦ LẠNH' => 30,
            'tu lanh' => 30,
            'Refrigerator' => 30,
            
            'Máy giặt' => 31,
            'Máy Giặt' => 31,
            'MÁY GIẶT' => 31,
            'may giat' => 31,
            'Washing Machine' => 31,
            
            'Điều hòa' => 32,
            'Điều Hòa' => 32,
            'ĐIỀU HÒA' => 32,
            'dieu hoa' => 32,
            'Air Conditioner' => 32,
            'Máy lạnh' => 32,
            'May lanh' => 32,
            'máy lạnh' => 32,
            
            'Nồi cơm' => 33,
            'Nồi Cơm' => 33,
            'NỒI CƠM' => 33,
            'noi com' => 33,
            'Rice Cooker' => 33,
            
            'Lò vi sóng' => 34,
            'Lò Vi Sóng' => 34,
            'LÒ VI SÓNG' => 34,
            'lo vi song' => 34,
            'Microwave' => 34,
            
            // TIVI (c_parent = 4) - MUST BE CHECKED EARLY
            'Smart Tivi LG' => 41,
            'Smart Tivi NanoCell LG' => 41,
            'Tivi LG' => 41,
            'LG OLED' => 41,
            'LG NanoCell' => 41,
            
            'Tivi Sony' => 42,
            'Sony TV' => 42,
            'SONY TV' => 42,
            'Sony Bravia' => 42,
            'Smart Tivi Sony' => 42,
            
            'Samsung TV' => 40,
            'SAMSUNG TV' => 40,
            'Smart Tivi Samsung' => 40,
            'Tivi Samsung' => 40,
            
            'Tivi QLED TCL' => 43,
            'Tivi TCL' => 43,
            'TCL TV' => 43,
            'TCL' => 43,
            
            // PHỤ KIỆN (c_parent = 5)
            'Tai nghe' => 50,
            'Tai Nghe' => 50,
            'TAI NGHE' => 50,
            'Headphone' => 50,
            'Earphone' => 50,
            'AirPods' => 50,
            
            'Chuột' => 51,
            'chuot' => 51,
            'Mouse' => 51,
            
            'Bàn phím' => 52,
            'Bàn Phím' => 52,
            'BÀN PHÍM' => 52,
            'ban phim' => 52,
            'Keyboard' => 52,
            
            'Loa' => 53,
            'loa' => 53,
            'Speaker' => 53,
            'LOA' => 53,
            
            'Sạc dự phòng' => 54,
            'Sạc Dự Phòng' => 54,
            'sac du phong' => 54,
            'Power Bank' => 54,
            'Pin dự phòng' => 54,
        ];
        
        $updatedCount = 0;
        $notFoundCount = 0;
        
        // Get all products
        $products = DB::table('products')->get();
        $totalProducts = $products->count();
        
        $this->command->info("📦 Tìm thấy {$totalProducts} sản phẩm trong database");
        
        if ($totalProducts == 0) {
            $this->command->error("❌ Không có sản phẩm nào trong database!");
            $this->command->info("💡 Bạn cần import dữ liệu từ duan.sql hoặc tạo sản phẩm mẫu.");
            return;
        }
        
        // Map products to categories
        foreach ($products as $product) {
            $mapped = false;
            
            // Try to match product name with keywords
            foreach ($mappings as $keyword => $categoryId) {
                if (stripos($product->pro_name, $keyword) !== false) {
                    // Update category
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['pro_category_id' => $categoryId]);
                    
                    $updatedCount++;
                    $mapped = true;
                    
                    $categoryName = $this->getCategoryName($categoryId);
                    $this->command->line("  ✅ [{$product->id}] {$product->pro_name} → {$categoryName}");
                    break; // Stop after first match
                }
            }
            
            if (!$mapped) {
                $notFoundCount++;
                if ($notFoundCount <= 10) { // Show first 10 only
                    $this->command->line("  ⚠️  [{$product->id}] {$product->pro_name} → Không map được");
                }
            }
        }
        
        $this->command->info("\n📊 KẾT QUẢ:");
        $this->command->info("  ✅ Đã map: {$updatedCount} sản phẩm");
        $this->command->info("  ⚠️  Chưa map: {$notFoundCount} sản phẩm");
        
        // Show category statistics
        $this->command->info("\n📈 THỐNG KÊ THEO CATEGORY:");
        $stats = DB::table('products')
            ->select('pro_category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('pro_category_id')
            ->orderBy('total', 'DESC')
            ->get();
        
        foreach ($stats as $stat) {
            $categoryName = $this->getCategoryName($stat->pro_category_id);
            $this->command->line("  • {$categoryName}: {$stat->total} sản phẩm");
        }
    }
    
    /**
     * Get category name by ID
     */
    private function getCategoryName($categoryId)
    {
        $category = DB::table('category')->where('id', $categoryId)->first();
        return $category ? $category->c_name : "Category ID {$categoryId}";
    }
}
