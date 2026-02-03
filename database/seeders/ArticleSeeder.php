<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $articles = [
            // Bài viết 1: Laptop Gaming
            [
                'a_name' => 'Top 5 Laptop Gaming Giá Tốt Năm 2026 - Đáng Mua Nhất',
                'a_slug' => Str::slug('Top 5 Laptop Gaming Giá Tốt Năm 2026'),
                'a_description' => 'Khám phá 5 mẫu laptop gaming có cấu hình mạnh mẽ, giá cả phải chăng, phù hợp cho game thủ và dân văn phòng.',
                'a_content' => '<h2>Top 5 Laptop Gaming Tốt Nhất 2026</h2>
<p>Nếu bạn đang tìm kiếm một chiếc laptop gaming với hiệu năng mạnh mẽ nhưng giá cả hợp lý, đây là 5 lựa chọn tốt nhất trong năm 2026.</p>

<h3>1. ASUS ROG Strix G15</h3>
<p><strong>Cấu hình:</strong></p>
<ul>
    <li>CPU: AMD Ryzen 7 6800H</li>
    <li>GPU: NVIDIA RTX 3060 6GB</li>
    <li>RAM: 16GB DDR5</li>
    <li>Màn hình: 15.6" FHD 144Hz</li>
</ul>
<p><strong>Giá:</strong> 28.990.000₫</p>

<h3>2. MSI GF63 Thin</h3>
<p>Laptop gaming mỏng nhẹ, phù hợp cho sinh viên và dân văn phòng. Cấu hình Intel Core i5 thế hệ 12, GTX 1650, giá chỉ 18.990.000₫.</p>

<h3>3. Acer Nitro 5</h3>
<p>Lựa chọn tốt nhất cho game thủ với ngân sách dưới 25 triệu. AMD Ryzen 5, RTX 3050, màn hình 144Hz.</p>

<p><strong>Kết luận:</strong> Tùy theo ngân sách và nhu cầu sử dụng, bạn có thể chọn laptop phù hợp. Hãy ghé cửa hàng chúng tôi để được tư vấn chi tiết!</p>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img1.jpg',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],

            // Bài viết 2: Điện thoại
            [
                'a_name' => 'iPhone 15 Pro Max vs Samsung Galaxy S24 Ultra - Nên Chọn Máy Nào?',
                'a_slug' => Str::slug('iPhone 15 Pro Max vs Samsung Galaxy S24 Ultra'),
                'a_description' => 'So sánh chi tiết 2 siêu phẩm flagship 2026: iPhone 15 Pro Max và Samsung Galaxy S24 Ultra. Ưu nhược điểm của từng máy.',
                'a_content' => '<h2>Cuộc Đua Flagship 2026</h2>
<p>iPhone 15 Pro Max và Samsung Galaxy S24 Ultra đều là những siêu phẩm đỉnh cao trong năm 2026. Vậy nên chọn máy nào?</p>

<h3>iPhone 15 Pro Max</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>✅ Chip A17 Pro siêu mạnh, xử lý mượt mà</li>
    <li>✅ Hệ sinh thái Apple hoàn hảo</li>
    <li>✅ Camera chất lượng cao, chụp đêm xuất sắc</li>
    <li>✅ Thiết kế Titanium cao cấp</li>
</ul>
<p><strong>Giá:</strong> 34.990.000₫</p>

<h3>Samsung Galaxy S24 Ultra</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>✅ Màn hình 6.8" AMOLED 120Hz tuyệt đẹp</li>
    <li>✅ Camera zoom 100x, chụp xa siêu nét</li>
    <li>✅ Bút S-Pen tiện lợi</li>
    <li>✅ Sạc nhanh 45W</li>
</ul>
<p><strong>Giá:</strong> 32.990.000₫</p>

<p><strong>Kết luận:</strong> Nếu bạn yêu thích iOS và hệ sinh thái Apple, chọn iPhone. Nếu thích Android và màn hình lớn, Samsung là lựa chọn tốt hơn.</p>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img2.jpg',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],

            // Bài viết 3: Điều hòa
            [
                'a_name' => 'Hướng Dẫn Chọn Mua Điều Hòa Tiết Kiệm Điện Cho Mùa Hè 2026',
                'a_slug' => Str::slug('Hướng Dẫn Chọn Mua Điều Hòa Tiết Kiệm Điện'),
                'a_description' => 'Bí quyết chọn điều hòa inverter tiết kiệm điện, phù hợp diện tích phòng, giá cả hợp lý cho mùa hè 2026.',
                'a_content' => '<h2>Chọn Điều Hòa Sao Cho Đúng?</h2>
<p>Mùa hè đến, nhiệt độ tăng cao, điều hòa trở thành thiết bị không thể thiếu. Nhưng làm sao để chọn được máy vừa mát, vừa tiết kiệm điện?</p>

<h3>1. Chọn Theo Diện Tích Phòng</h3>
<ul>
    <li>Phòng 12-15m²: Chọn máy 9.000 BTU</li>
    <li>Phòng 15-20m²: Chọn máy 12.000 BTU</li>
    <li>Phòng 20-25m²: Chọn máy 18.000 BTU</li>
</ul>

<h3>2. Ưu Tiên Máy Inverter</h3>
<p>Điều hòa Inverter tiết kiệm điện hơn 30-50% so với máy thường. Giá cao hơn một chút nhưng tiết kiệm tiền điện lâu dài.</p>

<h3>3. Thương Hiệu Uy Tín</h3>
<p><strong>Top 3 thương hiệu tốt nhất:</strong></p>
<ol>
    <li>Daikin (Nhật Bản) - Bền, tiết kiệm điện</li>
    <li>Mitsubishi (Nhật Bản) - Làm lạnh nhanh</li>
    <li>LG (Hàn Quốc) - Giá tốt, nhiều tính năng</li>
</ol>

<h3>Khuyến Mãi Đặc Biệt</h3>
<p>🎉 Mua điều hòa tại cửa hàng chúng tôi, giảm ngay 2 triệu + Tặng kèm quạt sạc!</p>

<p><strong>Liên hệ:</strong> Hotline 1900-xxxx để được tư vấn miễn phí!</p>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img3.jpg',
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
            ],

            // Bài viết 4: Tivi
            [
                'a_name' => 'Smart TV 4K Giá Rẻ Dưới 10 Triệu - Đáng Mua Nhất 2026',
                'a_slug' => Str::slug('Smart TV 4K Giá Rẻ Dưới 10 Triệu'),
                'a_description' => 'Top 5 mẫu smart TV 4K màn hình lớn, giá dưới 10 triệu, chất lượng tốt, phù hợp cho gia đình.',
                'a_content' => '<h2>Smart TV 4K Giá Rẻ 2026</h2>
<p>Bạn muốn sở hữu chiếc Smart TV 4K màn hình lớn nhưng ngân sách chỉ dưới 10 triệu? Đây là những lựa chọn tốt nhất!</p>

<h3>1. Samsung Crystal UHD 43" (43AU7700)</h3>
<ul>
    <li>✅ Độ phân giải 4K Ultra HD</li>
    <li>✅ Hệ điều hành Tizen OS</li>
    <li>✅ Hỗ trợ HDR</li>
    <li>✅ <strong>Giá: 7.990.000₫</strong></li>
</ul>

<h3>2. TCL 50" 4K Android TV</h3>
<ul>
    <li>✅ Màn hình 50 inch lớn</li>
    <li>✅ Android TV, kho ứng dụng khổng lồ</li>
    <li>✅ Dolby Audio</li>
    <li>✅ <strong>Giá: 8.490.000₫</strong></li>
</ul>

<h3>3. Xiaomi TV P1 55"</h3>
<ul>
    <li>✅ 55 inch giá cực tốt</li>
    <li>✅ Android TV 10</li>
    <li>✅ Viền mỏng, thiết kế đẹp</li>
    <li>✅ <strong>Giá: 9.990.000₫</strong></li>
</ul>

<p><strong>Lưu ý:</strong> Khi mua TV, nên chọn bảo hành chính hãng và mua tại đại lý uy tín!</p>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img4.jpg',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],

            // Bài viết 5: Tủ lạnh
            [
                'a_name' => 'Tủ Lạnh Inverter - Tiết Kiệm Điện, Bảo Quản Thực Phẩm Tươi Lâu',
                'a_slug' => Str::slug('Tủ Lạnh Inverter Tiết Kiệm Điện'),
                'a_description' => 'Tìm hiểu về công nghệ Inverter trên tủ lạnh, lợi ích và cách chọn tủ lạnh phù hợp cho gia đình.',
                'a_content' => '<h2>Tại Sao Nên Chọn Tủ Lạnh Inverter?</h2>
<p>Tủ lạnh Inverter đang trở thành xu hướng được nhiều gia đình Việt lựa chọn. Vậy Inverter là gì và lợi ích ra sao?</p>

<h3>Công Nghệ Inverter Là Gì?</h3>
<p>Inverter là công nghệ điều chỉnh công suất máy nén linh hoạt theo nhiệt độ thực tế, giúp:</p>
<ul>
    <li>✅ Tiết kiệm điện 30-50%</li>
    <li>✅ Vận hành êm ái, ít tiếng ồn</li>
    <li>✅ Bảo quản thực phẩm tươi lâu hơn</li>
    <li>✅ Tuổi thọ máy nén cao hơn</li>
</ul>

<h3>Top 3 Tủ Lạnh Inverter Bán Chạy</h3>

<h4>1. Panasonic NR-BV368 (322L)</h4>
<p>Tủ lạnh 2 cửa ngăn đá trên, công nghệ Econavi tiết kiệm điện thông minh.</p>
<p><strong>Giá: 11.990.000₫</strong></p>

<h4>2. Samsung RT38K (380L)</h4>
<p>Digital Inverter, làm lạnh đa chiều 360°, thiết kế hiện đại.</p>
<p><strong>Giá: 13.490.000₫</strong></p>

<h4>3. LG GN-L315S (315L)</h4>
<p>Smart Inverter, kháng khuẩn khử mùi, giá tốt.</p>
<p><strong>Giá: 10.990.000₫</strong></p>

<h3>Mẹo Sử Dụng Tiết Kiệm Điện</h3>
<ol>
    <li>Đặt tủ lạnh ở nơi thoáng mát, tránh ánh nắng trực tiếp</li>
    <li>Không để thực phẩm nóng vào tủ</li>
    <li>Kiểm tra gioăng cửa thường xuyên</li>
    <li>Rã đông định kỳ (nếu không có công nghệ No Frost)</li>
</ol>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img5.jpg',
                'created_at' => $now->copy()->subDays(12),
                'updated_at' => $now->copy()->subDays(12),
            ],

            // Bài viết 6: Máy giặt
            [
                'a_name' => 'Máy Giặt Cửa Trước vs Cửa Trên - Nên Chọn Loại Nào?',
                'a_slug' => Str::slug('Máy Giặt Cửa Trước vs Cửa Trên'),
                'a_description' => 'So sánh ưu nhược điểm giữa máy giặt cửa trước và cửa trên, giúp bạn chọn được loại phù hợp nhất.',
                'a_content' => '<h2>Máy Giặt Cửa Trước vs Cửa Trên</h2>
<p>Khi mua máy giặt, nhiều người băn khoăn không biết nên chọn cửa trước hay cửa trên. Hãy cùng phân tích chi tiết!</p>

<h3>Máy Giặt Cửa Trước</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>✅ Giặt sạch hơn, ít hao mòn quần áo</li>
    <li>✅ Tiết kiệm nước 30-40%</li>
    <li>✅ Vắt khô tốt hơn (1200-1400 vòng/phút)</li>
    <li>✅ Có thể xếp chồng hoặc làm mặt bàn bếp</li>
</ul>

<p><strong>Nhược điểm:</strong></p>
<ul>
    <li>❌ Giá cao hơn cửa trên</li>
    <li>❌ Thời gian giặt lâu hơn</li>
    <li>❌ Cần cúi xuống để cho đồ vào</li>
</ul>

<h3>Máy Giặt Cửa Trên</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>✅ Giá rẻ hơn</li>
    <li>✅ Giặt nhanh hơn</li>
    <li>✅ Dễ cho đồ vào (không cần cúi)</li>
    <li>✅ Bảo trì đơn giản</li>
</ul>

<p><strong>Nhược điểm:</strong></p>
<ul>
    <li>❌ Tốn nước hơn</li>
    <li>❌ Hao mòn quần áo nhiều hơn</li>
    <li>❌ Vắt khô kém hơn</li>
</ul>

<h3>Nên Chọn Loại Nào?</h3>
<blockquote>
<p><strong>Chọn cửa trước</strong> nếu: Gia đình đông người, giặt nhiều, muốn giặt sạch và tiết kiệm nước.</p>
<p><strong>Chọn cửa trên</strong> nếu: Ngân sách eo hẹp, cần máy giặt nhanh, người già sử dụng.</p>
</blockquote>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img6.jpg',
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(15),
            ],

            // Bài viết 7: Khuyến mãi
            [
                'a_name' => '🎉 MEGA SALE Tháng 1/2026 - Giảm Đến 50% Toàn Bộ Sản Phẩm!',
                'a_slug' => Str::slug('MEGA SALE Tháng 1 2026 Giảm 50%'),
                'a_description' => 'Chương trình khuyến mãi lớn nhất năm! Giảm giá sốc đến 50% tất cả sản phẩm điện tử, điện lạnh. Số lượng có hạn!',
                'a_content' => '<h2>🔥 MEGA SALE - SỰ KIỆN MUA SẮM LỚN NHẤT NĂM!</h2>
<p><strong>Thời gian:</strong> 01/01/2026 - 31/01/2026</p>

<h3>🎁 Ưu Đãi Khủng:</h3>

<h4>📱 Điện Thoại & Laptop</h4>
<ul>
    <li>✅ iPhone 15 Pro Max: Giảm 5 triệu</li>
    <li>✅ Samsung Galaxy S24: Giảm 4 triệu</li>
    <li>✅ Laptop Gaming: Giảm đến 7 triệu</li>
</ul>

<h4>❄️ Điều Hòa & Tủ Lạnh</h4>
<ul>
    <li>✅ Điều hòa Daikin Inverter: Giảm 3 triệu</li>
    <li>✅ Tủ lạnh Panasonic: Giảm 2 triệu</li>
    <li>✅ Máy giặt LG Inverter: Giảm 1.5 triệu</li>
</ul>

<h4>📺 Smart TV</h4>
<ul>
    <li>✅ Samsung 55" 4K: Giảm 4 triệu</li>
    <li>✅ LG OLED 65": Giảm 10 triệu</li>
    <li>✅ Sony Bravia: Giảm 6 triệu</li>
</ul>

<h3>🎁 QUÀ TẶNG KÈM:</h3>
<ol>
    <li>Tặng phiếu mua hàng 500K cho đơn từ 10 triệu</li>
    <li>Tặng quà tặng công nghệ (tai nghe, chuột, bàn phím...)</li>
    <li>Miễn phí vận chuyển toàn quốc</li>
    <li>Bảo hành VIP 24 tháng</li>
</ol>

<h3>💳 HỖ TRỢ TRẢ GÓP:</h3>
<ul>
    <li>✅ Trả góp 0% lãi suất</li>
    <li>✅ Duyệt nhanh trong 5 phút</li>
    <li>✅ Không cần thẻ tín dụng</li>
</ul>

<h3>📞 LIÊN HỆ NGAY:</h3>
<p><strong>Hotline:</strong> 1900-xxxx</p>
<p><strong>Website:</strong> www.cuahangcuatoi.vn</p>
<p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận XYZ, TP.HCM</p>

<p style="color: red; font-size: 18px;"><strong>⏰ SỐ LƯỢNG CÓ HẠN - NHANH TAY KẺO LỠ!</strong></p>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img7.jpg',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],

            // Bài viết 8: Bếp từ
            [
                'a_name' => 'Bếp Từ vs Bếp Gas - Nên Chọn Loại Nào Cho Gia Đình?',
                'a_slug' => Str::slug('Bếp Từ vs Bếp Gas'),
                'a_description' => 'So sánh chi tiết bếp từ và bếp gas: ưu nhược điểm, chi phí, độ an toàn. Giúp bạn đưa ra lựa chọn đúng đắn.',
                'a_content' => '<h2>Bếp Từ vs Bếp Gas</h2>
<p>Xu hướng sử dụng bếp từ đang ngày càng phổ biến ở Việt Nam. Vậy bếp từ có thực sự tốt hơn bếp gas?</p>

<h3>Bếp Từ</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>✅ An toàn tuyệt đối (không khí gas, không cháy nổ)</li>
    <li>✅ Tiết kiệm năng lượng 60%</li>
    <li>✅ Vệ sinh dễ dàng (mặt kính phẳng)</li>
    <li>✅ Nấu nhanh, hiệu suất cao</li>
    <li>✅ Nhà bếp mát mẻ hơn</li>
</ul>

<p><strong>Nhược điểm:</strong></p>
<ul>
    <li>❌ Giá cao (từ 3-10 triệu)</li>
    <li>❌ Chỉ dùng nồi từ tính</li>
    <li>❌ Phụ thuộc điện (mất điện không nấu được)</li>
</ul>

<h3>Bếp Gas</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
    <li>✅ Giá rẻ (từ 1-3 triệu)</li>
    <li>✅ Dùng mọi loại nồi</li>
    <li>✅ Không phụ thuộc điện</li>
    <li>✅ Quen thuộc với người Việt</li>
</ul>

<p><strong>Nhược điểm:</strong></p>
<ul>
    <li>❌ Nguy hiểm (rò rỉ gas, cháy nổ)</li>
    <li>❌ Tốn gas, kém hiệu quả</li>
    <li>❌ Làm nóng nhà bếp</li>
    <li>❌ Vệ sinh khó khăn</li>
</ul>

<h3>Kết Luận</h3>
<blockquote>
<p><strong>Chọn bếp từ</strong> nếu: Ưu tiên an toàn, tiết kiệm, vệ sinh dễ, có ngân sách.</p>
<p><strong>Chọn bếp gas</strong> nếu: Ngân sách thấp, nhà hay mất điện, quen dùng gas.</p>
</blockquote>

<p><strong>Gợi ý:</strong> Nhiều gia đình hiện dùng cả 2 loại để linh hoạt!</p>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img8.jpg',
                'created_at' => $now->copy()->subDays(18),
                'updated_at' => $now->copy()->subDays(18),
            ],

            // Bài viết 9: Loa Bluetooth
            [
                'a_name' => 'Top 7 Loa Bluetooth Mini Giá Rẻ - Âm Thanh Chất Lượng Cao',
                'a_slug' => Str::slug('Top 7 Loa Bluetooth Mini Giá Rẻ'),
                'a_description' => 'Khám phá 7 mẫu loa Bluetooth mini giá dưới 1 triệu, chất lượng âm thanh tốt, thiết kế đẹp, pin trâu.',
                'a_content' => '<h2>Loa Bluetooth Mini - Nhỏ Gọn Nhưng Chất Lượng</h2>
<p>Bạn muốn nghe nhạc mọi lúc, mọi nơi? Loa Bluetooth mini chính là lựa chọn hoàn hảo! Dưới đây là top 7 mẫu giá tốt nhất.</p>

<h3>1. JBL Go 3</h3>
<ul>
    <li>Thiết kế nhỏ gọn, nhiều màu sắc</li>
    <li>Chống nước IP67</li>
    <li>Pin 5 giờ</li>
    <li><strong>Giá: 690.000₫</strong></li>
</ul>

<h3>2. Sony SRS-XB13</h3>
<ul>
    <li>Bass mạnh mẽ, âm thanh trong trẻo</li>
    <li>Chống nước IP67</li>
    <li>Pin 16 giờ</li>
    <li><strong>Giá: 990.000₫</strong></li>
</ul>

<h3>3. Anker Soundcore Mini 3</h3>
<ul>
    <li>EQ tùy chỉnh qua app</li>
    <li>PartyCast kết nối 100+ loa</li>
    <li>Pin 15 giờ</li>
    <li><strong>Giá: 850.000₫</strong></li>
</ul>

<h3>4. Xiaomi Mi Portable Bluetooth Speaker</h3>
<ul>
    <li>Thiết kế tròn, gọn nhẹ</li>
    <li>Giá rẻ nhất</li>
    <li>Pin 10 giờ</li>
    <li><strong>Giá: 390.000₫</strong></li>
</ul>

<h3>Lưu Ý Khi Mua</h3>
<ol>
    <li><strong>Công suất:</strong> Từ 3W-5W là đủ cho cá nhân</li>
    <li><strong>Pin:</strong> Nên chọn pin trên 10 giờ</li>
    <li><strong>Chống nước:</strong> IPX4 trở lên nếu dùng ngoài trời</li>
    <li><strong>Kết nối:</strong> Bluetooth 5.0 trở lên cho ổn định</li>
</ol>',
                'a_active' => 1,
                'a_avatar' => '/storage/photos/shares/Blog/blog-img9.jpg',
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(20),
            ],
        ];

        foreach ($articles as $article) {
            DB::table('article')->insert($article);
        }

        $this->command->info('✅ Đã tạo ' . count($articles) . ' bài viết tin tức về sản phẩm điện tử!');
    }
}
