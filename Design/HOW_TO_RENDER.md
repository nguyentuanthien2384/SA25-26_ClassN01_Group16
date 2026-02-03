# 🎨 HOW TO RENDER C4 DIAGRAMS - STEP BY STEP

## ⚡ FASTEST METHOD (5 minutes)

### Step 1: Open PlantUML Online

**URL:** https://www.plantuml.com/plantuml/uml/

Browser sẽ hiển thị editor với:
- Left panel: Code editor
- Right panel: Diagram preview

---

### Step 2: Render Level 1 (System Context)

**Action:**
1. Mở file: `Design/c4-level1-context.puml`
2. Select All (Ctrl+A)
3. Copy (Ctrl+C)
4. Paste vào PlantUML Online editor
5. Diagram tự động render! ✨

**Expected result:**

```
Bạn sẽ thấy:
┌─────────────┐
│  Customer   │ ──▶ [ElectroShop] ──▶ [Payment Gateway]
└─────────────┘           │
                          └──▶ [Email Service]
```

**Download:**
- Click **"PNG"** button → Download PNG file
- Click **"SVG"** button → Download SVG file (better quality!)

**Save as:** `c4-level1-system-context.png`

---

### Step 3: Render Level 2 (Container)

**Action:**
1. Mở file: `Design/c4-level2-container.puml`
2. Copy all content (Ctrl+A, Ctrl+C)
3. Paste vào PlantUML Online
4. Diagram renders! ✨

**Expected result:**

```
Complex diagram showing:
- Web Frontend
- Kong API Gateway
- 5 Microservices
- Databases (MySQL, Redis, Elasticsearch)
- Monitoring tools
- All connections
```

**Download:**
- Click **"PNG"** → Save
- **Save as:** `c4-level2-container.png`

---

### Step 4: Render Level 3 (Component)

**Action:**
1. Mở file: `Design/c4-level3-catalog-component.puml`
2. Copy all content
3. Paste vào PlantUML Online
4. Diagram renders! ✨

**Expected result:**

```
Catalog Service internals:
- Controllers
- Services
- Repositories
- Models
- Database connections
```

**Download:**
- Click **"PNG"** → Save
- **Save as:** `c4-level3-catalog-component.png`

---

## 🎨 ADVANCED: VS Code Method

### Prerequisites

**Install:**
1. **Visual Studio Code** (if not installed)
2. **PlantUML extension**
   - Open VS Code
   - Ctrl+Shift+X (Extensions)
   - Search: "PlantUML"
   - Install: "PlantUML" by jebbs

3. **Java JDK** (required!)
   - Download: https://adoptium.net/
   - Install Java 11 or higher

4. **Graphviz** (optional, better layouts)
   - Download: https://graphviz.org/download/
   - Install and add to PATH

---

### Render in VS Code

**Step 1: Open file**
```
File → Open → Design/c4-level1-context.puml
```

**Step 2: Preview**
```
Press: Alt+D
Or: Right-click → "Preview Current Diagram"
```

**Step 3: Export**
```
Right-click on preview → Export Current Diagram
Choose format: PNG, SVG, PDF
```

**Advantages:**
- ✅ Real-time preview as you edit
- ✅ No internet needed (after setup)
- ✅ Multiple export formats
- ✅ Higher quality output

---

## 🖼️ DRAW.IO METHOD (Visual Editing)

### Step 1: Open Draw.io

**URL:** https://app.diagrams.net/

Or use desktop app (better for large diagrams)

---

### Step 2: Create New Diagram

1. Click **"Create New Diagram"**
2. Choose **"Blank Diagram"**
3. Name it: **"ElectroShop-C4-Context"**

---

### Step 3: Import C4 Shapes

**Method 1: Use built-in library**
1. Click **"More Shapes"** (bottom left)
2. Search: **"C4"**
3. Enable: **"C4 (Architecture)"**
4. Click **"Apply"**

**Method 2: Import custom library**
1. Click: **File → Open Library from → URL**
2. Enter: `https://raw.githubusercontent.com/plantuml-stdlib/C4-PlantUML/master/C4.xml`
3. Click: **"OK"**

---

### Step 4: Create Diagram

**For System Context:**

1. **Add Actors:**
   - Drag "Person" shape
   - Label: "Customer"
   - Add description below
   - Repeat for Admin, Guest

2. **Add System:**
   - Drag "System" shape
   - Label: "ElectroShop Platform"
   - Add description

3. **Add External Systems:**
   - Drag "External System" shape
   - Label: "Payment Gateways"
   - Repeat for Email Service

4. **Connect:**
   - Use arrow tool
   - Draw from Customer to ElectroShop
   - Double-click arrow to add label: "Browse, search, purchase"
   - Add technology: "[HTTPS/Web]"

5. **Style:**
   - Select shapes → Format panel
   - Use consistent colors:
     - Person: Blue (#1168BD)
     - System: Green (#6DB33F)
     - External: Gray (#999999)

6. **Add Title:**
   - Insert → Text box
   - Type: "System Context Diagram - ElectroShop"
   - Font size: 18-24pt
   - Bold

7. **Add Legend:**
   - Insert → Rectangle
   - Add text:
     ```
     Legend:
     [Person] = User/Actor
     [System] = Software System
     → = Uses/Interacts
     ```

---

### Step 5: Export

**For high-quality export:**

1. **Select All** (Ctrl+A)
2. **File → Export as → PNG**
3. **Settings:**
   - ✅ Zoom: 200%
   - ✅ Border: 10px
   - ✅ Transparent Background: No
   - ✅ Grid: No
4. **Click "Export"**
5. **Save as:** `c4-level1-system-context.png`

**Also export as:**
- SVG (scalable, best for printing)
- PDF (for reports)

---

## 📏 SIZING RECOMMENDATIONS

### For Lab Reports

**PNG Export:**
- Width: 1920px
- Height: Auto
- DPI: 300
- Format: PNG with white background

### For README

**PNG Export:**
- Width: 1200px
- Height: Auto
- DPI: 150
- Format: PNG or SVG

### For Presentations

**PNG Export:**
- Width: 1920px (Full HD)
- Height: 1080px (16:9 ratio)
- DPI: 150
- Format: PNG

---

## 🎨 STYLING GUIDE

### Official C4 Colors

**Person (Actors):**
- Background: `#08427B`
- Border: `#073B6F`
- Text: White

**System:**
- Background: `#1168BD`
- Border: `#0B4884`
- Text: White

**Container:**
- Background: `#438DD5`
- Border: `#3C7FC0`
- Text: White

**Component:**
- Background: `#85BBF0`
- Border: `#78A8D8`
- Text: Black

**External System:**
- Background: `#999999`
- Border: `#8A8A8A`
- Text: White

**Database:**
- Background: `#438DD5`
- Border: `#3C7FC0`
- Text: White
- Shape: Cylinder

---

### Fonts

**Recommended:**
- **Title:** Arial Bold, 18-24pt
- **Labels:** Arial, 12-14pt
- **Descriptions:** Arial, 10-12pt

---

### Arrows

**Styles:**
- **Synchronous calls:** Solid line with arrow
- **Async/Events:** Dashed line with arrow
- **Bi-directional:** Double arrows

**Labels:**
- Add relationship description
- Add technology/protocol in [brackets]

**Example:**
```
Customer ────▶ ElectroShop
       Browse products
       [HTTPS/Web Browser]
```

---

## 🧪 QUALITY CHECKLIST

**Before finalizing diagrams:**

- [ ] All text is readable (font ≥ 12pt)
- [ ] No overlapping elements
- [ ] Arrows point in correct direction
- [ ] All boxes have labels
- [ ] All arrows have descriptions
- [ ] Colors are consistent
- [ ] Legend included
- [ ] Title present
- [ ] No spelling errors
- [ ] Technology stack mentioned

**All diagrams should:**
- [ ] Use official C4 notation
- [ ] Be consistent across levels
- [ ] Include sufficient detail
- [ ] Not be too cluttered
- [ ] Be understandable without explanation

---

## 📖 EXAMPLES

### Example 1: System Context Text

**Template:**
```
[Person: Customer]
    Uses
    ↓
[System: ElectroShop Platform]
    Description: E-commerce platform for electronic products
    Technology: Laravel Microservices
    
    Integrates with
    ↓
[External: Payment Gateway]
    Description: Process payments
    Technology: VNPay, MoMo, PayPal
```

---

### Example 2: Container Relationships

**Template:**
```
[Container: Web Frontend]
    Technology: Laravel Blade, Vue.js
    
    Makes API calls
    ↓
[Container: Kong API Gateway]
    Technology: Kong 3.4
    Responsibilities:
    - Rate limiting (100 req/min)
    - CORS
    - Authentication
    
    Routes to
    ↓
[Container: Catalog Service]
    Technology: Laravel 10
    Database: MySQL + Elasticsearch
```

---

### Example 3: Component Flow

**Template:**
```
[Component: ProductController]
    Handles HTTP requests
    ↓
[Component: ProductService]
    Business logic + validation
    ↓
[Component: ProductRepository]
    Data access layer
    ↓
[Component: Product Model]
    ORM entity
    ↓
[Database: MySQL]
```

---

## 💡 PRO TIPS

### Tip 1: Start Simple

- Create Level 1 first
- Get feedback
- Then add Level 2
- Finally add Level 3 if needed

### Tip 2: Use ASCII for Drafts

- Quick to create
- Easy to edit
- Good for discussions
- Convert to visual later

### Tip 3: Export Multiple Formats

- PNG for embedding in docs
- SVG for web/scaling
- PDF for printing
- Keep source (.puml) in git

### Tip 4: Version Control

```bash
git add Design/
git commit -m "Add C4 Model architecture diagrams"
```

### Tip 5: Link Diagrams

**In README.md:**
```markdown
## Architecture

![System Context](Design/c4-level1-system-context.png)

For details, see [Architecture Documentation](Design/C4_MODEL_DIAGRAMS.md)
```

---

## 🆘 COMMON ISSUES

### Issue 1: PlantUML không render

**Symptom:** Blank or error message

**Fix:**
- Check internet connection (needs to load C4 library)
- Check syntax errors
- Make sure `!include` line is at top
- Try different browser

---

### Issue 2: Diagram quá nhỏ

**Fix in PlantUML:**
```plantuml
@startuml
!include ...

scale 1.5  ' ← Add this to make 1.5x bigger

' Your diagram
@enduml
```

**Fix in Draw.io:**
- Select All → Format → Size
- Increase width/height
- Or zoom in before export

---

### Issue 3: Text bị cắt

**Fix:**
- Make shape bigger
- Use shorter text
- Use line breaks in descriptions

---

### Issue 4: Arrows overlap

**Fix in PlantUML:**
```plantuml
' Add direction hints
Rel_U(from, to)  ' Up
Rel_D(from, to)  ' Down
Rel_L(from, to)  ' Left
Rel_R(from, to)  ' Right
```

**Fix in Draw.io:**
- Manually adjust arrow routing
- Use waypoints
- Change arrow style

---

## ✅ FINAL CHECKLIST

**Rendering complete when:**

- [ ] ✅ Level 1 rendered as PNG (1920x1080)
- [ ] ✅ Level 2 rendered as PNG (1920x1080)
- [ ] ✅ Level 3 rendered as PNG (1920x1080)
- [ ] ✅ All PNGs saved in `Design/` folder
- [ ] ✅ Diagrams added to Lab report
- [ ] ✅ Diagrams added to README
- [ ] ✅ Source `.puml` files committed to git

---

## 🎯 EXPECTED OUTPUT

After rendering all diagrams, you should have:

```
Design/
├── c4-level1-context.puml              (Source)
├── c4-level1-system-context.png        (Rendered) ⭐
│
├── c4-level2-container.puml            (Source)
├── c4-level2-container.png             (Rendered) ⭐
│
├── c4-level3-catalog-component.puml    (Source)
├── c4-level3-catalog-component.png     (Rendered) ⭐
│
└── C4_MODEL_DIAGRAMS.md                (Documentation)
```

---

## 🚀 NEXT STEP

**DO THIS NOW (5 minutes):**

1. Visit: https://www.plantuml.com/plantuml/uml/
2. Open: `c4-level1-context.puml`
3. Copy all → Paste
4. Download PNG
5. Repeat for Level 2 & 3

**DONE!** 3 diagrams ready for your report! 🎉

---

**Status:** ✅ Ready to render!
