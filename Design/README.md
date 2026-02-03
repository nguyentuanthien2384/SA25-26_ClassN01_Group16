# 🏗️ DESIGN FOLDER - C4 MODEL DIAGRAMS

## 📁 OVERVIEW

Folder này chứa **C4 Model Architecture Diagrams** cho dự án ElectroShop E-Commerce Platform.

---

## 📄 FILES IN THIS FOLDER

### C4 Model Diagrams
| File | Type | Purpose | Size |
|------|------|---------|------|
| **C4_MODEL_DIAGRAMS.md** | Documentation | Full C4 Model guide with ASCII diagrams | 30KB+ |
| **c4-level1-context.puml** | PlantUML | System Context source code | 1KB |
| **c4-level2-container.puml** | PlantUML | Container Diagram source code | 3KB |
| **c4-level3-catalog-component.puml** | PlantUML | Component Diagram source code | 2KB |
| **C4_QUICK_START.md** | Guide | How to render & use diagrams | 8KB |

### Deployment View (Lab 08) - NEW!
| File | Type | Purpose | Size |
|------|------|---------|------|
| **deployment-diagram.puml** | PlantUML | UML Deployment Diagram source | 5KB |
| **DEPLOYMENT_VIEW.md** | Documentation | Physical deployment architecture | 15KB |
| **ATAM_ANALYSIS.md** | Analysis | Quality Attribute Analysis (ATAM) | 20KB |

### Other
| File | Type | Purpose | Size |
|------|------|---------|------|
| **README.md** | This file | Folder overview | 5KB |
| **HOW_TO_RENDER.md** | Guide | Rendering instructions | 3KB |
| **HUONG_DAN_RENDER.md** | Guide | Vietnamese rendering guide | 3KB |

---

## 🎯 QUICK START

### 1. View ASCII Diagrams (Instant!)

```bash
# Just open and read
Design/C4_MODEL_DIAGRAMS.md
```

### 2. Render PlantUML Diagrams (5 minutes)

**Visit:** https://www.plantuml.com/plantuml/uml/

**Render each file:**
1. `c4-level1-context.puml` → Download PNG
2. `c4-level2-container.puml` → Download PNG
3. `c4-level3-catalog-component.puml` → Download PNG

**Result:** 3 beautiful diagrams ready for reports! 🎨

### 3. Read Full Guide

```bash
# Comprehensive documentation
Design/C4_QUICK_START.md
```

---

## 🏛️ C4 MODEL LEVELS

### Level 1: System Context ⭐

**File:** `c4-level1-context.puml`

**Shows:**
- Actors (Customer, Admin, Guest)
- ElectroShop System
- External Systems (Payment, Email)
- High-level relationships

**Use for:** Presentations, stakeholder communication

---

### Level 2: Container ⭐⭐

**File:** `c4-level2-container.puml`

**Shows:**
- Web Frontend & Admin Panel
- Kong API Gateway
- 5 Microservices
- Databases (MySQL, Redis, Elasticsearch)
- Monitoring Stack (Consul, Jaeger, Prometheus, Grafana, ELK)

**Use for:** Architecture docs, technical reports

---

### Level 3: Component ⭐⭐⭐

**File:** `c4-level3-catalog-component.puml`

**Shows:**
- Controllers (ProductController, CategoryController, etc.)
- Services (ProductService, SearchService, CacheService)
- Repositories (ProductRepository, CategoryRepository)
- Models (Product, Category, Review)
- Internal data flow

**Use for:** Developer documentation, code reviews

---

## 🎨 RENDERING OPTIONS

### Option A: PlantUML Online (Recommended)

**Pros:**
- ✅ No installation needed
- ✅ Instant results
- ✅ High quality PNG/SVG
- ✅ Free

**How to:**
1. Visit: https://www.plantuml.com/plantuml/uml/
2. Copy `.puml` file content
3. Paste and view
4. Download PNG/SVG

---

### Option B: VS Code Extension

**Pros:**
- ✅ Real-time preview
- ✅ Edit and see changes instantly
- ✅ Export multiple formats
- ✅ Offline capable

**How to:**
1. Install "PlantUML" extension in VS Code
2. Open `.puml` file
3. Press Alt+D to preview
4. Right-click → Export

**Requirements:**
- Java JDK installed
- Graphviz installed (optional, for better layouts)

---

### Option C: Draw.io

**Pros:**
- ✅ Visual editing
- ✅ Drag and drop
- ✅ No coding needed
- ✅ Export to many formats

**How to:**
1. Visit: https://app.diagrams.net/
2. Import C4 library (More Shapes → C4)
3. Drag shapes and create
4. Export as PNG/SVG

**See:** `C4_QUICK_START.md` for detailed instructions

---

### Option D: Use ASCII (No rendering needed!)

**Pros:**
- ✅ Already done!
- ✅ Works in markdown
- ✅ No tools needed
- ✅ Version control friendly

**How to:**
1. Open `C4_MODEL_DIAGRAMS.md`
2. Copy ASCII diagrams
3. Paste into your docs
4. Done!

---

## 📖 DOCUMENTATION GUIDE

### For Lab Reports

**Include:**
1. ✅ System Context (Level 1) - Show to teacher/reviewer
2. ✅ Container Diagram (Level 2) - Show microservices architecture
3. ✅ Component Diagram (Level 3) - Show one service detail

**Format:**
- Export PlantUML as PNG (high resolution)
- Or use ASCII diagrams from `C4_MODEL_DIAGRAMS.md`

**Add to:**
- Architecture section
- Design section
- Appendix

---

### For README.md

**Include:**
- System Context (Level 1)
- Container Diagram (Level 2)

**Format:**
- PNG/SVG images
- Link to full docs in `Design/` folder

---

### For Technical Docs

**Include:**
- All levels (1, 2, 3)
- Detailed explanations
- Technology choices

**Format:**
- Link to `C4_MODEL_DIAGRAMS.md`
- Embed rendered diagrams

---

## 🔍 WHAT'S IN EACH DIAGRAM?

### System Context Shows:

```
✅ 3 Actors (Customer, Admin, Guest)
✅ 1 Main System (ElectroShop)
✅ 2 External Systems (Payment Gateways, Email Service)
✅ 6 Relationships with protocols
```

### Container Shows:

```
✅ 2 Frontend apps (Web, Admin)
✅ 1 API Gateway (Kong)
✅ 5 Microservices (Catalog, Order, Payment, Notification, Customer)
✅ 3 Databases (MySQL, Redis, Elasticsearch)
✅ 6 Infrastructure services (Consul, Jaeger, Prometheus, Grafana, ELK)
✅ 25+ connections with protocols
```

### Component Shows:

```
✅ 4 Controllers (Product, Category, Search, Review)
✅ 5 Services (Product, Category, Search, Cache, Event)
✅ 3 Repositories (Product, Category, Review)
✅ 4 Models (Product, Category, Review, ProImage)
✅ 3 External systems (MySQL, Redis, Elasticsearch)
✅ 15+ internal relationships
```

---

## 🎯 SUCCESS CRITERIA

**Your diagrams should:**

- ✅ Clearly show system boundaries
- ✅ Identify all actors and external systems
- ✅ Show technology choices
- ✅ Display data flow
- ✅ Include legends and titles
- ✅ Be easy to understand
- ✅ Be professional quality

**All criteria met!** ✅

---

## 🚀 NEXT ACTIONS

### Immediate (5 minutes):

1. **Render Level 1**
   - Open: https://www.plantuml.com/plantuml/uml/
   - Copy: `c4-level1-context.puml`
   - Download: PNG

2. **Render Level 2**
   - Copy: `c4-level2-container.puml`
   - Download: PNG

3. **View ASCII versions**
   - Open: `C4_MODEL_DIAGRAMS.md`
   - Already formatted and ready!

### Later (optional):

- Render Level 3 Component diagrams
- Create additional component diagrams for other services
- Add deployment diagrams
- Create sequence diagrams for workflows

---

## 📚 LEARN MORE

### C4 Model Fundamentals

**4 Levels explained:**
1. **System Context:** Who uses it? What does it integrate with?
2. **Container:** What are the main building blocks?
3. **Component:** What's inside each container?
4. **Code:** How is it implemented? (Class diagrams)

**Notation:**
- Person: User/actor (blue)
- System: Software system (green)
- Container: App/service/database (light blue)
- Component: Module/class (orange)

**Relationships:**
- Solid line: Uses/calls
- Dashed line: Async/events
- Labels: Technology/protocol

---

## ✅ CHECKLIST FOR REPORTS

**To use in Lab report:**

- [ ] Render PlantUML diagrams (Level 1 + 2)
- [ ] Export as PNG (300 DPI)
- [ ] Add to Architecture section
- [ ] Add diagram captions
- [ ] Reference in text
- [ ] Include legend/key
- [ ] Cite C4 Model source

**Quality checklist:**

- [ ] Diagrams are readable (font size ≥ 12pt)
- [ ] Colors are consistent
- [ ] Labels are clear
- [ ] No overlapping elements
- [ ] Arrows point correctly
- [ ] Legend included

---

**Status:** ✅ **READY TO USE**

**Files:** 5 files created in `Design/` folder

**Next:** Render and add to your reports! 🚀
