<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeedDashboard extends Command
{
    protected $signature = 'dashboard:seed {count=30 : So luong don hang mau}';
    protected $description = 'Tao du lieu mau cho dashboard (don hang, danh gia, lien he)';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        if ($count <= 0) {
            $this->error('So luong phai > 0');
            return self::FAILURE;
        }

        $faker = Faker::create('vi_VN');

        // Users
        $userIds = DB::table('users')->pluck('id')->all();
        if (empty($userIds)) {
            for ($i = 1; $i <= 10; $i++) {
                $name = $faker->name;
                $userIds[] = DB::table('users')->insertGetId([
                    'name' => $name . ' ' . $i,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'active' => 1,
                    'password' => bcrypt('123456'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Products
        $productIds = DB::table('products')->pluck('id')->all();
        if (empty($productIds)) {
            for ($i = 1; $i <= 12; $i++) {
                $name = 'San pham mau ' . $i;
                $data = [
                    'pro_name' => $name,
                    'pro_slug' => Str::slug($name) . '-' . $i,
                    'pro_content' => $faker->paragraph(3),
                    'pro_category_id' => 0,
                    'pro_price' => $faker->numberBetween(100000, 5000000),
                    'pro_author_id' => 0,
                    'pro_sale' => 0,
                    'pro_active' => 1,
                    'pro_hot' => 0,
                    'pro_view' => 0,
                    'pro_description' => $faker->sentence(12),
                    'pro_image' => null,
                    'pro_title_seo' => null,
                    'pro_description_seo' => null,
                    'pro_keyword_seo' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $productIds[] = DB::table('products')->insertGetId($data);
            }
        }

        // Transactions + Orders
        for ($i = 1; $i <= $count; $i++) {
            $userId = $faker->randomElement($userIds);
            $total = $faker->numberBetween(200000, 5000000);
            $statusPool = [0, 1, 1, 1, 2]; // uu tien trang thai da xu ly
            $status = $faker->randomElement($statusPool);
            $time = Carbon::now()->subDays($faker->numberBetween(0, 29));

            $transactionData = [
                'tr_user_id' => $userId,
                'tr_note' => $faker->sentence(6),
                'tr_address' => $faker->address,
                'tr_phone' => $faker->phoneNumber,
                'tr_total' => $total,
                'tr_status' => $status,
                'created_at' => $time,
                'updated_at' => $time,
            ];

            if (Schema::hasColumn('transactions', 'tr_payment_method')) {
                $transactionData['tr_payment_method'] = 'cod';
            }
            if (Schema::hasColumn('transactions', 'tr_payment_status')) {
                $transactionData['tr_payment_status'] = $status === 1 ? 1 : 0;
            }

            $transactionId = DB::table('transactions')->insertGetId($transactionData);

            $orderCount = $faker->numberBetween(1, 3);
            for ($j = 1; $j <= $orderCount; $j++) {
                $productId = $faker->randomElement($productIds);
                $quantity = $faker->numberBetween(1, 3);
                $price = $faker->numberBetween(100000, 3000000);
                $orderData = [
                    'od_transaction_id' => $transactionId,
                    'od_product_id' => $productId,
                    'od_quantity' => $quantity,
                    'od_price' => $price,
                    'od_sale' => 0,
                    'created_at' => $time,
                    'updated_at' => $time,
                ];
                if (Schema::hasColumn('oders', 'od_cart_id')) {
                    $orderData['od_cart_id'] = 0;
                }
                DB::table('oders')->insert($orderData);
            }
        }

        // Ratings
        for ($i = 1; $i <= 15; $i++) {
            DB::table('rating')->insert([
                'ra_product_id' => $faker->randomElement($productIds),
                'ra_number' => $faker->numberBetween(1, 5),
                'ra_content' => $faker->sentence(10),
                'ra_user_id' => $faker->randomElement($userIds),
                'created_at' => Carbon::now()->subDays($faker->numberBetween(0, 29)),
                'updated_at' => Carbon::now()->subDays($faker->numberBetween(0, 29)),
            ]);
        }

        // Contacts
        for ($i = 1; $i <= 10; $i++) {
            DB::table('contacts')->insert([
                'con_name' => $faker->name,
                'con_phone' => $faker->phoneNumber,
                'con_email' => $faker->safeEmail,
                'con_title' => $faker->sentence(6),
                'con_message' => $faker->paragraph(2),
                'con_status' => 0,
                'created_at' => Carbon::now()->subDays($faker->numberBetween(0, 29)),
                'updated_at' => Carbon::now()->subDays($faker->numberBetween(0, 29)),
            ]);
        }

        $this->info("Da tao du lieu mau cho dashboard ({$count} don hang).");
        return self::SUCCESS;
    }
}
