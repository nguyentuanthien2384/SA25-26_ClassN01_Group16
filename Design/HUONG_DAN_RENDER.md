# 🎨 HƯỚNG DẪN RENDER C4 DIAGRAMS - CHI TIẾT

## ⚡ RENDER TRONG 5 PHÚT!

### 🎯 MỤC TIÊU

Tạo 3 file PNG đẹp từ 3 file PlantUML (.puml) để:
- ✅ Add vào báo cáo Lab
- ✅ Add vào README.md
- ✅ Dùng cho thuyết trình

---

## 📋 CHUẨN BỊ

### Bạn cần:
- ✅ Trình duyệt web (Chrome/Edge/Firefox)
- ✅ Internet (để truy cập PlantUML Online)
- ✅ 3 file .puml đã có sẵn trong folder `Design/`

### Files cần render:
1. ✅ `c4-level1-context.puml` → System Context Diagram
2. ✅ `c4-level2-container.puml` → Container Diagram
3. ✅ `c4-level3-catalog-component.puml` → Component Diagram

---

## 🚀 BƯỚC 1: MỞ PLANTUML ONLINE

### 1.1. Mở trình duyệt

Mở **Chrome**, **Edge**, hoặc **Firefox**

### 1.2. Truy cập PlantUML Online

**URL:** https://www.plantuml.com/plantuml/uml/

Bạn sẽ thấy trang web có:
- **Bên trái:** Code editor (màu trắng)
- **Bên phải:** Preview diagram (sẽ hiện sau khi paste code)

---

## 🎨 BƯỚC 2: RENDER LEVEL 1 - SYSTEM CONTEXT

### 2.1. Mở file source

**Trong VS Code:**
```
1. Mở: Design/c4-level1-context.puml
2. Select All (Ctrl+A)
3. Copy (Ctrl+C)
```

**Hoặc mở bằng Notepad:**
```
1. Right-click file → Open with → Notepad
2. Ctrl+A → Select all
3. Ctrl+C → Copy
```

### 2.2. Paste vào PlantUML Online

**Quay lại trình duyệt:**
```
1. Click vào ô editor (bên trái)
2. Xóa code mẫu (nếu có)
3. Paste code đã copy (Ctrl+V)
```

### 2.3. Xem diagram

**Diagram tự động render!** 🎉

Bạn sẽ thấy:
- 👥 3 hình người (Customer, Admin, Guest)
- 💻 1 hệ thống (ElectroShop)
- 🌐 2 hệ thống ngoài (Payment, Email)
- ➡️ Các mũi tên kết nối

**Nếu không thấy diagram:**
- Chờ 2-3 giây
- Hoặc click nút "Submit" (nếu có)
- Check lại code có copy đầy đủ không

### 2.4. Download PNG

**Tìm nút download:**
- Phía trên hoặc dưới diagram
- Tìm nút **"PNG"** hoặc **"Download PNG"**
- Click vào

**File sẽ được download:**
- Tên mặc định: `diagram.png` hoặc tương tự
- **Đổi tên thành:** `c4-level1-system-context.png`
- **Lưu vào:** `Design/diagrams/` (tạo folder mới nếu chưa có)

### 2.5. Download SVG (Optional - Better quality!)

**Nếu muốn chất lượng cao:**
- Click nút **"SVG"** thay vì PNG
- SVG = Vector, phóng to không bị mờ
- Tốt cho in ấn, presentation

---

## 🏗️ BƯỚC 3: RENDER LEVEL 2 - CONTAINER

### 3.1. Mở file source

```
1. Mở: Design/c4-level2-container.puml
2. Ctrl+A (Select all)
3. Ctrl+C (Copy)
```

### 3.2. Paste vào PlantUML Online

```
1. Quay lại trình duyệt (tab PlantUML)
2. Xóa hết code cũ (Ctrl+A, Delete)
3. Paste code mới (Ctrl+V)
```

### 3.3. Xem diagram

**Diagram phức tạp hơn Level 1!**

Bạn sẽ thấy:
- 🖥️ Web Frontend + Admin Panel
- 🚪 Kong API Gateway
- 🔧 5 Microservices (Catalog, Order, Payment, Notification, Customer)
- 💾 3 Databases (MySQL, Redis, Elasticsearch)
- 📊 Monitoring Stack (Consul, Jaeger, Prometheus, Grafana)
- ➡️ Rất nhiều mũi tên kết nối

**Tips:**
- Diagram này to! Có thể cần zoom out để thấy hết
- Chất lượng vẫn tốt khi zoom out

### 3.4. Download

```
1. Click "PNG"
2. Đổi tên: c4-level2-container.png
3. Lưu vào: Design/diagrams/
```

---

## 🔧 BƯỚC 4: RENDER LEVEL 3 - COMPONENT

### 4.1. Mở file source

```
1. Mở: Design/c4-level3-catalog-component.puml
2. Ctrl+A
3. Ctrl+C
```

### 4.2. Paste và render

```
1. Xóa code cũ trong PlantUML Online
2. Paste code mới
3. Chờ diagram render
```

### 4.3. Xem diagram

**Chi tiết bên trong Catalog Service:**

- 🎮 Controllers (ProductController, CategoryController, SearchController)
- ⚙️ Services (ProductService, SearchService, CacheService)
- 📦 Repositories (ProductRepository, CategoryRepository)
- 🗃️ Models (Product, Category, Review)
- 🔌 Connections to databases

### 4.4. Download

```
1. Click "PNG"
2. Đổi tên: c4-level3-catalog-component.png
3. Lưu vào: Design/diagrams/
```

---

## 📁 BƯỚC 5: TỔ CHỨC FILES

### 5.1. Tạo folder diagrams

**Trong VS Code hoặc File Explorer:**
```
1. Vào folder: Design/
2. Tạo folder mới: diagrams/
3. Move 3 file PNG vào đây
```

### 5.2. Kết quả cuối cùng

**Structure:**
```
Design/
├── diagrams/                              ← Folder mới tạo
│   ├── c4-level1-system-context.png      ← ⭐ File 1
│   ├── c4-level2-container.png           ← ⭐ File 2
│   └── c4-level3-catalog-component.png   ← ⭐ File 3
│
├── c4-level1-context.puml                ← Source
├── c4-level2-container.puml              ← Source
├── c4-level3-catalog-component.puml      ← Source
├── C4_MODEL_DIAGRAMS.md
├── C4_QUICK_START.md
└── README.md
```

---

## 📝 BƯỚC 6: DÙNG DIAGRAMS

### 6.1. Add vào báo cáo Lab (Word/PDF)

**Trong Microsoft Word:**
```
1. Insert → Pictures
2. Chọn: Design/diagrams/c4-level1-system-context.png
3. Resize để vừa trang
4. Add caption: "Hình X: System Context Diagram"
5. Lặp lại cho 2 diagram còn lại
```

**Tips:**
- Dùng "Wrap Text: In line with text"
- Center align
- Font caption: 11pt, italic

---

### 6.2. Add vào README.md (GitHub)

**Edit README.md:**
```markdown
# ElectroShop E-Commerce Platform

## 🏗️ Kiến trúc hệ thống

### System Context

Sơ đồ tổng quan hệ thống, actors và external systems:

![System Context](Design/diagrams/c4-level1-system-context.png)

### Container Diagram

Kiến trúc microservices với 5 services, API Gateway, và databases:

![Container Diagram](Design/diagrams/c4-level2-container.png)

### Component Diagram

Chi tiết bên trong Catalog Service:

![Component Diagram](Design/diagrams/c4-level3-catalog-component.png)

## 📚 Chi tiết

Xem full documentation tại [C4 Model Documentation](Design/C4_MODEL_DIAGRAMS.md)
```

**Commit lên GitHub:**
```bash
git add Design/
git commit -m "Add C4 Model architecture diagrams"
git push
```

---

### 6.3. Add vào PowerPoint (Thuyết trình)

**Trong PowerPoint:**
```
1. Insert → Pictures → Browse
2. Chọn diagram
3. Resize để full slide
4. Add title slide: "Kiến trúc hệ thống"
5. Add notes phía dưới để giải thích
```

**Gợi ý slides:**
```
Slide 1: Title - "Kiến trúc Microservices - ElectroShop"
Slide 2: System Context (giải thích actors)
Slide 3: Container Diagram (giải thích services)
Slide 4: Component Diagram (giải thích một service)
Slide 5: Technology Stack
```

---

## ✅ CHECKLIST

### Render diagrams
- [ ] ✅ Đã render Level 1 (System Context)
- [ ] ✅ Đã render Level 2 (Container)
- [ ] ✅ Đã render Level 3 (Component)
- [ ] ✅ Đã lưu 3 file PNG vào `Design/diagrams/`
- [ ] ✅ Đã đổi tên file đúng format

### Sử dụng diagrams
- [ ] ⏳ Đã add vào báo cáo Lab
- [ ] ⏳ Đã add vào README.md
- [ ] ⏳ Đã commit lên GitHub
- [ ] ⏳ Đã chuẩn bị slides (nếu cần)

### Quality check
- [ ] ⏳ Diagrams rõ ràng, dễ đọc
- [ ] ⏳ Kích thước phù hợp (không quá nhỏ)
- [ ] ⏳ Có caption/mô tả
- [ ] ⏳ Không bị mờ/vỡ hình

---

## 🆘 XỬ LÝ LỖI

### Lỗi 1: PlantUML không render

**Triệu chứng:**
- Paste code nhưng không thấy diagram
- Báo lỗi syntax

**Nguyên nhân:**
- Code copy thiếu
- Internet chậm
- Browser cache

**Cách fix:**
```
1. Copy lại code từ đầu (Ctrl+A trong file .puml)
2. Clear browser cache (Ctrl+Shift+Delete)
3. Refresh page (F5)
4. Paste lại
```

---

### Lỗi 2: Diagram quá nhỏ/mờ

**Triệu chứng:**
- PNG download về bị nhỏ
- Khi zoom in bị mờ

**Cách fix:**

**Option 1: Download SVG thay vì PNG**
```
- SVG = Vector, không bị mờ
- Dùng trong Word, PowerPoint tốt hơn
```

**Option 2: Increase zoom trong PlantUML**
- Thêm dòng này vào đầu file .puml:
```plantuml
@startuml
scale 1.5  ← Thêm dòng này (1.5x bigger)
!include ...
```

**Option 3: Export PNG với DPI cao**
- Một số tools cho chọn DPI
- Chọn 300 DPI thay vì 72 DPI

---

### Lỗi 3: Diagram không vừa trang báo cáo

**Cách fix trong Word:**
```
1. Right-click image → Size and Position
2. Width: 15cm (A4 portrait) hoặc 20cm (landscape)
3. Keep aspect ratio: Checked
4. OK
```

---

### Lỗi 4: GitHub không hiển thị diagram

**Nguyên nhân:**
- Path sai trong markdown
- File không commit

**Cách fix:**
```bash
# Check file có tồn tại
ls Design/diagrams/

# Commit files
git add Design/diagrams/*.png
git commit -m "Add diagram images"
git push

# Check path trong README.md
# Đúng: ![Diagram](Design/diagrams/context.png)
# Sai:  ![Diagram](diagrams/context.png)  ← thiếu Design/
```

---

## 💡 TIPS & TRICKS

### Tip 1: Tạo thumbnail nhỏ

**Dùng cho trang index, overview:**
```
- Resize diagram về 400x300px
- Lưu thành *-thumb.png
- Load nhanh hơn
```

### Tip 2: Dark mode diagrams

**Nếu muốn background đen:**
```plantuml
@startuml
!include ...

skinparam backgroundColor #1E1E1E
skinparam defaultTextColor white

' Your diagram
@enduml
```

### Tip 3: Export multiple formats

**Download cả PNG và SVG:**
- PNG: Dùng cho web, GitHub
- SVG: Dùng cho Word, PowerPoint
- PDF: Dùng cho báo cáo in

### Tip 4: Version diagrams

**Khi update kiến trúc:**
```
diagrams/
├── v1/
│   └── c4-level1-system-context.png
├── v2/
│   └── c4-level1-system-context.png  ← Updated
└── latest/
    └── c4-level1-system-context.png  ← Symlink to v2
```

---

## 📊 SIZE RECOMMENDATIONS

### Cho báo cáo Lab (Word/PDF)

**PNG Export:**
- Width: 15-20cm
- Height: Auto
- Resolution: 300 DPI
- Format: PNG with white background

**Trong Word:**
- Position: Center
- Text wrap: In line with text
- Caption: Below image

---

### Cho README (GitHub)

**PNG Export:**
- Width: 1200px
- Height: Auto
- Resolution: 150 DPI
- Format: PNG

**Trong Markdown:**
```markdown
<p align="center">
  <img src="Design/diagrams/diagram.png" alt="Diagram" width="800">
</p>
```

---

### Cho PowerPoint

**PNG Export:**
- Width: 1920px (Full HD)
- Height: 1080px (16:9)
- Resolution: 150 DPI
- Format: PNG

**Trong slide:**
- Full screen
- Add title on top
- Add notes below

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành, bạn sẽ có:

### ✅ 3 Diagrams PNG chất lượng cao

```
✅ c4-level1-system-context.png    (Big picture)
✅ c4-level2-container.png         (Microservices)
✅ c4-level3-catalog-component.png (Service internals)
```

### ✅ Sẵn sàng dùng

```
✅ Add vào báo cáo Lab
✅ Add vào README.md (GitHub)
✅ Add vào slides thuyết trình
✅ Professional & impressive!
```

### ✅ Follow best practices

```
✅ Chuẩn C4 Model (industry standard)
✅ Rõ ràng, dễ hiểu
✅ Chất lượng cao
✅ Đầy đủ 3 levels
```

---

## 🚀 NEXT STEPS

**Sau khi có diagrams:**

### 1. Add vào báo cáo Lab (Priority 1!)
```
- Architecture section
- Design section
- Appendix
```

### 2. Update README.md
```markdown
## Architecture

![Architecture](Design/diagrams/c4-level2-container.png)

Xem chi tiết: [C4 Model Docs](Design/C4_MODEL_DIAGRAMS.md)
```

### 3. Chuẩn bị thuyết trình
```
- PowerPoint slides
- 1 slide per diagram
- Add explanations
```

### 4. Commit lên GitHub
```bash
git add Design/diagrams/*.png
git commit -m "Add C4 architecture diagrams"
git push
```

---

## 🎉 HOÀN THÀNH!

**Chúc mừng!** 🎊

Bạn đã có **professional architecture diagrams** cho dự án!

**Những gì bạn đạt được:**
- ✅ 3 C4 Model diagrams chuẩn quốc tế
- ✅ Chất lượng cao, professional
- ✅ Sẵn sàng dùng cho báo cáo, thuyết trình
- ✅ Ấn tượng với thầy/cô, reviewer

**Thời gian đã tiết kiệm:**
- ❌ Không cần học Structurizr (3 giờ)
- ❌ Không cần viết code (2 giờ)
- ❌ Không cần setup tools (1 giờ)
- ✅ Chỉ mất 5 phút render!

**Good luck với Lab! 🚀**

---

**Created:** 2026-01-28  
**Status:** ✅ Ready to use  
**Next:** Render diagrams ngay!
