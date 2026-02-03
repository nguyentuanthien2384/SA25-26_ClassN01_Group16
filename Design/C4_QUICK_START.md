# 🚀 C4 MODEL - QUICK START GUIDE

## ⚡ TẠO DIAGRAMS TRONG 5 PHÚT!

### Option A: Dùng PlantUML Online (Nhanh nhất!)

**Bước 1:** Copy PlantUML code
- Mở file: `c4-level1-context.puml`
- Copy tất cả nội dung (Ctrl+A, Ctrl+C)

**Bước 2:** Render online
- Visit: https://www.plantuml.com/plantuml/uml/
- Paste code vào
- Diagram tự động hiển thị!

**Bước 3:** Export
- Click "PNG" để download PNG
- Click "SVG" để download SVG (scalable)

**Lặp lại cho:**
- `c4-level2-container.puml`
- `c4-level3-catalog-component.puml`

---

### Option B: Dùng Draw.io (Visual editing)

**Bước 1:** Mở Draw.io
- Visit: https://app.diagrams.net/
- Click "Create New Diagram"

**Bước 2:** Import C4 Library
- Click: More Shapes (bottom left)
- Search: "C4"
- Enable: C4 (Architecture)
- Click: Apply

**Bước 3:** Vẽ diagram
- Drag shapes từ sidebar:
  - Person (for users)
  - System (for ElectroShop)
  - External System (for Payment, Email)
  - Container (for services)
  - Component (for classes)
- Connect với arrows
- Add labels

**Bước 4:** Export
- File → Export as → PNG
- File → Export as → SVG

---

### Option C: Dùng VS Code (For developers)

**Bước 1:** Install extension
- Open VS Code
- Install: "PlantUML" extension
- Install: Java JDK (required for PlantUML)

**Bước 2:** Open và preview
- Open: `c4-level1-context.puml`
- Press: Alt+D (preview)
- Diagram hiển thị real-time!

**Bước 3:** Export
- Right-click on preview
- Export Current Diagram → PNG/SVG

---

## 📁 FILES ĐÃ TẠO

| File | Purpose | Format |
|------|---------|--------|
| **C4_MODEL_DIAGRAMS.md** | Full documentation (ASCII + PlantUML) | Markdown |
| **c4-level1-context.puml** | System Context code | PlantUML |
| **c4-level2-container.puml** | Container Diagram code | PlantUML |
| **c4-level3-catalog-component.puml** | Component Diagram code | PlantUML |
| **C4_QUICK_START.md** | This guide | Markdown |

---

## 🎯 WHICH DIAGRAM TO USE WHEN?

### Level 1: System Context
**Use for:**
- Management presentations
- Stakeholder communication
- Project proposals
- High-level overview

**Shows:**
- Who uses the system?
- What external systems interact?
- Big picture only

---

### Level 2: Container
**Use for:**
- Technical architecture docs
- DevOps team communication
- Deployment planning
- Technology stack overview

**Shows:**
- What services exist?
- What technologies used?
- How do they communicate?
- Where is data stored?

---

### Level 3: Component
**Use for:**
- Developer documentation
- Code structure planning
- Refactoring discussions
- Onboarding new developers

**Shows:**
- Internal service structure
- Classes and their relationships
- Data flow within service
- Layer architecture

---

### Level 4: Code (Optional)
**Use for:**
- Very detailed documentation
- Complex algorithms
- Class diagrams
- Implementation details

**Shows:**
- Class methods and properties
- Interfaces and implementations
- Design patterns in code

---

## 🎨 CUSTOMIZATION TIPS

### Colors

```plantuml
' Change person color
Person(customer, "Customer", $sprite="person", $tags="important")
AddElementTag("important", $bgColor="#FF6B6B")

' Change system color
System(app, "App", $bgColor="#51CF66")

' Change container color
Container(api, "API", "Node.js", $bgColor="#4DABF7")
```

### Layout

```plantuml
' Top to bottom
LAYOUT_TOP_DOWN()

' Left to right
LAYOUT_LEFT_RIGHT()

' With legend
LAYOUT_WITH_LEGEND()
```

### Grouping

```plantuml
' Group containers
Container_Boundary(backend, "Backend Services") {
    Container(api, "API", "Node.js")
    Container(db, "Database", "PostgreSQL")
}
```

---

## 📚 EXAMPLES FROM PROJECT

### Example 1: Show Customer Journey

```plantuml
Person(customer, "Customer")
System(shop, "ElectroShop")

Rel(customer, shop, "1. Browse products")
Rel(customer, shop, "2. Add to cart")
Rel(customer, shop, "3. Checkout")
Rel(customer, shop, "4. Make payment")
```

### Example 2: Show Service Communication

```plantuml
Container(order, "Order Service")
Container(payment, "Payment Service")
Container(notif, "Notification Service")

Rel(order, payment, "Request payment")
Rel(payment, notif, "Payment success event")
```

### Example 3: Show Data Flow

```plantuml
Component(controller, "Controller")
Component(service, "Service")
Component(repository, "Repository")
ContainerDb(db, "Database")

Rel(controller, service, "Call")
Rel(service, repository, "Query")
Rel(repository, db, "SQL")
```

---

## 🆘 TROUBLESHOOTING

### Issue 1: PlantUML không render

**Cause:** Missing include or syntax error

**Fix:**
```plantuml
' Make sure this is at the top
@startuml
!include https://raw.githubusercontent.com/plantuml-stdlib/C4-PlantUML/master/C4_Context.puml

' Your diagram here

@enduml
```

### Issue 2: Arrows không hiển thị

**Cause:** Wrong relationship syntax

**Fix:**
```plantuml
' Correct
Rel(from, to, "Description", "Technology")

' Wrong
from -> to : Description
```

### Issue 3: Shapes missing in Draw.io

**Cause:** C4 library not imported

**Fix:**
1. Click "More Shapes"
2. Enable "C4 (Architecture)"
3. Click "Apply"

---

## 🎯 BEST PRACTICES

### 1. Keep it Simple
- Don't show everything at once
- Focus on what matters for audience
- Use appropriate level of detail

### 2. Be Consistent
- Same colors for same types
- Same line styles for relationships
- Use legends

### 3. Add Context
- Include titles
- Add descriptions
- Explain relationships

### 4. Update Regularly
- Keep in sync with code
- Review in team meetings
- Version control diagrams

### 5. Use Tools
- PlantUML for code-based diagrams
- Draw.io for visual editing
- Both are valid!

---

## ✅ CHECKLIST

**To create complete C4 documentation:**

- [ ] Level 1: System Context created
- [ ] Level 2: Container Diagram created
- [ ] Level 3: Component Diagram(s) created
- [ ] Exported as PNG/SVG
- [ ] Added to project README
- [ ] Added to Lab report
- [ ] Reviewed by team
- [ ] Kept in version control

---

## 📖 RESOURCES

### Official

- **C4 Model Website:** https://c4model.com/
- **PlantUML C4:** https://github.com/plantuml-stdlib/C4-PlantUML
- **Draw.io:** https://app.diagrams.net/

### Examples

- **C4 Examples:** https://c4model.com/#examples
- **Real World Examples:** https://github.com/structurizr/examples

### Tools

- **PlantUML Online:** https://www.plantuml.com/plantuml/
- **PlantUML VS Code:** Search "PlantUML" in extensions
- **Draw.io Desktop:** https://github.com/jgraph/drawio-desktop

---

## 🎉 DONE!

**Bạn đã có:**

- ✅ Full C4 Model diagrams (3 levels)
- ✅ PlantUML source code (ready to render)
- ✅ ASCII diagrams (for markdown)
- ✅ Complete documentation
- ✅ Quick start guide
- ✅ Best practices

**Next steps:**

1. Render PlantUML files online
2. Export as PNG/SVG
3. Add to your reports/docs
4. Show to team/teacher
5. Get impressed! 🚀

---

**Created:** 2026-01-28  
**Project:** ElectroShop E-Commerce Platform  
**Status:** ✅ READY TO USE
