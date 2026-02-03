<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanOldArticlesSeeder extends Seeder
{
    /**
     * Xóa bài viết Lorem ipsum cũ, chỉ giữ lại bài viết thật về sản phẩm
     */
    public function run(): void
    {
        $this->command->info('🧹 Bắt đầu xóa bài viết Lorem ipsum cũ...');
        
        // Danh sách từ khóa Lorem ipsum cần xóa
        $loremKeywords = [
            'Lorem',
            'Ipsum',
            'Dolor',
            'Occaecati',
            'Numquam',
            'Atque',
            'Fugiat',
            'Voluptate',
            'OCCAECATI',
            'NUMQUAM',
            'ATQUE',
            'FUGIAT',
            'rerum omnis',
            'magnam provident',
            'suscipit laboriosam'
        ];
        
        $deletedCount = 0;
        
        // Xóa từng loại Lorem ipsum
        foreach ($loremKeywords as $keyword) {
            $count = DB::table('article')
                ->where('a_name', 'LIKE', "%{$keyword}%")
                ->delete();
            
            $deletedCount += $count;
        }
        
        // Xóa các bài có description chứa Lorem ipsum
        $count = DB::table('article')
            ->where('a_description', 'LIKE', '%ipsum%')
            ->orWhere('a_description', 'LIKE', '%lorem%')
            ->orWhere('a_description', 'LIKE', '%fugiat%')
            ->delete();
        
        $deletedCount += $count;
        
        $this->command->info("✅ Đã xóa {$deletedCount} bài viết Lorem ipsum cũ!");
        
        // Đếm số bài viết còn lại
        $remaining = DB::table('article')->count();
        $this->command->info("✅ Còn lại {$remaining} bài viết thật về sản phẩm!");
        
        // Hiển thị danh sách bài viết còn lại
        $this->command->info("\n📝 Danh sách bài viết còn lại:");
        $articles = DB::table('article')
            ->select('id', 'a_name', 'created_at')
            ->orderBy('id', 'DESC')
            ->get();
        
        foreach ($articles as $article) {
            $this->command->line("  • [{$article->id}] {$article->a_name}");
        }
    }
}
