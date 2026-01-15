<?php

namespace App\Console\Commands;

use App\Models\Models\Article;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeedArticles extends Command
{
    protected $signature = 'articles:seed {count=100 : So luong bai viet can tao}';
    protected $description = 'Tao nhanh bai viet mau de hien thi tin tuc noi bat';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        if ($count <= 0) {
            $this->error('So luong phai > 0');
            return self::FAILURE;
        }

        $faker = Faker::create('vi_VN');
        $now = Carbon::now();

        for ($i = 1; $i <= $count; $i++) {
            $title = $faker->sentence(6);
            $uniqueSuffix = $now->format('YmdHis') . '-' . $i;
            $name = $title . ' ' . $uniqueSuffix;

            $article = new Article();
            $article->a_name = $name;
            $article->a_slug = Str::slug($name);
            $article->a_description = $faker->paragraph(2);
            $article->a_content = $faker->paragraphs(4, true);
            $article->a_active = 1;
            if (Schema::hasColumn('article', 'a_author_id')) {
                $article->a_author_id = 0;
            }
            if (Schema::hasColumn('article', 'a_description_seo')) {
                $article->a_description_seo = $faker->sentence(12);
            }
            if (Schema::hasColumn('article', 'a_title_seo')) {
                $article->a_title_seo = $faker->sentence(8);
            }
            if (Schema::hasColumn('article', 'a_avatar')) {
                $article->a_avatar = null;
            }
            if (Schema::hasColumn('article', 'a_view')) {
                $article->a_view = $faker->numberBetween(0, 500);
            }

            $randomDays = $faker->numberBetween(0, 29);
            $createdAt = $now->copy()->subDays($randomDays)->subMinutes($faker->numberBetween(0, 1440));
            $article->created_at = $createdAt;
            $article->updated_at = $createdAt;

            $article->save();
        }

        $this->info("Da tao xong {$count} bai viet.");
        return self::SUCCESS;
    }
}
