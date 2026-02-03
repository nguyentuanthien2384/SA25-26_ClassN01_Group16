<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeepOnlyNewArticlesSeeder extends Seeder
{
    /**
     * Chỉ giữ lại 9 bài viết mới nhất về sản phẩm điện tử
     * Xóa tất cả bài viết còn lại
     */
    public function run(): void
    {
        $this->command->info('🧹 Xóa TẤT CẢ bài viết cũ, chỉ giữ 9 bài viết mới nhất...');
        
        // Lấy ID của 9 bài viết mới nhất (về sản phẩm điện tử)
        $keepArticles = [
            'Top 5 Laptop Gaming',
            'iPhone 15 Pro Max',
            'Hướng Dẫn Chọn Mua Điều Hòa',
            'Smart TV 4K',
            'Tủ Lạnh Inverter',
            'Máy Giặt Cửa Trước',
            'MEGA SALE',
            'Bếp Từ vs Bếp Gas',
            'Top 7 Loa Bluetooth'
        ];
        
        // Lấy danh sách ID cần giữ
        $keepIds = [];
        foreach ($keepArticles as $keyword) {
            $article = DB::table('article')
                ->where('a_name', 'LIKE', "%{$keyword}%")
                ->orderBy('id', 'DESC')
                ->first();
            
            if ($article) {
                $keepIds[] = $article->id;
                $this->command->line("  ✅ Giữ lại: [{$article->id}] {$article->a_name}");
            }
        }
        
        if (empty($keepIds)) {
            $this->command->error('❌ Không tìm thấy bài viết mới nào! Hủy thao tác.');
            return;
        }
        
        // Đếm số bài sẽ xóa
        $deleteCount = DB::table('article')
            ->whereNotIn('id', $keepIds)
            ->count();
        
        $this->command->info("\n🗑️  Sẽ xóa {$deleteCount} bài viết cũ...");
        
        // Xóa tất cả bài viết trừ 9 bài mới
        DB::table('article')
            ->whereNotIn('id', $keepIds)
            ->delete();
        
        // Kiểm tra kết quả
        $remaining = DB::table('article')->count();
        
        $this->command->info("\n✅ Hoàn tất!");
        $this->command->info("✅ Đã xóa {$deleteCount} bài viết cũ");
        $this->command->info("✅ Còn lại {$remaining} bài viết về sản phẩm điện tử");
        
        // Hiển thị danh sách cuối cùng
        $this->command->info("\n📝 Danh sách bài viết cuối cùng:");
        $articles = DB::table('article')
            ->select('id', 'a_name')
            ->orderBy('created_at', 'DESC')
            ->get();
        
        foreach ($articles as $i => $article) {
            $this->command->line(($i + 1) . ". [{$article->id}] {$article->a_name}");
        }
    }
}
