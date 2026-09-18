# 🎨 Arise ERP — UI & Theme Guideline (Compact, Sharp-Edge & Mobile-First)

## 1. Design Overview & Objectives
* **Philosophy**: Modern, clean, ultra-fast, high-density Enterprise ERP dashboard.
* **Complete Removal of AdminLTE**: Replace the 1.56 MB bloated `adminlte.min.css` and 100 KB `adminlte.js` with a custom, ultra-fast, lightweight CSS (~30 KB) and zero-dependency sidebar script.
* **Layout Density**: Compact layout with tighter padding, high information density, and readable typography.
* **Sharp Aesthetic**: Crisp, sharp corners (`border-radius: 2px - 3px`) on all cards, buttons, input fields, badges, and modals.
* **Mobile-First Experience**: Off-canvas responsive slide drawer for sidebar on mobile/tablets with a backdrop overlay, touch targets, and fluid responsive containers.
* **100% Behavioral Compatibility**: All existing classes (`.main-sidebar`, `.nav-sidebar`, `.has-treeview`, `.menu-open`, `.nav-treeview`, `.card`, `.btn`, `.content-wrapper`, `data-widget="pushmenu"`, Select2, DataTables, Modals) will continue working with zero JavaScript breakages.

---

## 2. Design Tokens & Variables

### Color Palette
| Token | Hex Value | Usage |
| :--- | :--- | :--- |
| `--arise-primary` | `#002C54` | Primary branding, top-bar, main buttons |
| `--arise-primary-hover` | `#001f3d` | Hover state for primary elements |
| `--arise-secondary` | `#0f3460` | Sidebar active background, secondary nav |
| `--arise-accent` | `#2563eb` | Links, focus rings, interactive accents |
| `--arise-success` | `#10b981` | Success alerts, active badges, status |
| `--arise-warning` | `#f59e0b` | Pending status, warnings, attention |
| `--arise-danger` | `#ef4444` | Errors, delete buttons, absent status |
| `--arise-info` | `#0284c7` | Info badges, notifications |
| `--arise-bg-body` | `#f8fafc` | Page workspace background (Slate 50) |
| `--arise-bg-card` | `#ffffff` | Card and modal surfaces |
| `--arise-border` | `#e2e8f0` | Standard border color (crisp 1px line) |
| `--arise-border-dark` | `#cbd5e1` | Input field borders, table header borders |
| `--arise-text-main` | `#0f172a` | Primary headings, table text, input text |
| `--arise-text-muted` | `#64748b` | Secondary text, captions, subtitles |

### Geometry & Sharp Edges
| Element | Radius Value | Notes |
| :--- | :--- | :--- |
| **Buttons** (`.btn`) | `3px` | Sharp, crisp, modern enterprise edges |
| **Cards** (`.card`) | `3px` | Sharp corners with subtle 1px border |
| **Form Inputs** (`.form-control`, `.select2`) | `3px` | Clean rectangular boxes |
| **Badges** (`.badge`) | `2px` | Compact rectangular indicators |
| **Dropdowns & Modals** | `3px` | Flat, sharp, modern floating surfaces |
| **Tables** | `0px` | Crisp rectangular grids with 1px border |

### Compact Typography & Spacing
* **Font Family**: `'Segoe UI', -apple-system, BlinkMacSystemFont, 'Inter', Roboto, sans-serif`
* **Base Font Size**: `13px` (compact, high-density ERP)
* **Body Line Height**: `1.42`
* **Headings**:
  * H1 (Page Titles): `18px - 20px`, Semi-bold (`600`)
  * H2 (Section Titles): `16px`, Semi-bold (`600`)
  * H3 / Card Titles: `14px`, Semi-bold (`600`)
  * H4 / Subtitles: `13px`, Medium (`500`)
  * Small Text / Badges / Footnotes: `11px - 12px`
* **Form & Button Sizing**:
  * Input Height: `32px` (reduced from bulky 42px)
  * Input Padding: `4px 8px`
  * Button Padding: `4px 12px` (`font-size: 13px`)
  * Button Sm: `2px 8px` (`font-size: 11px`)
  * Table Cell Padding: `6px 10px` (dense, easy scanning)

---

## 3. Layout Architecture

### Desktop Layout ($\ge 992\text{px}$)
```
+-------------------------------------------------------------------------+
| [LOGO / BRAND] | [TOGGLE] Top Navigation Bar: Session, Branch, Profile |
+-------------------------------------------------------------------------+
|                |                                                        |
|  SIDEBAR       |  PAGE CONTENT WRAPPER (.content-wrapper)               |
|  - Dashboard   |  +--------------------------------------------------+  |
|  - Students >  |  | Breadcrumb & Page Header                         |  |
|    - Admission |  +--------------------------------------------------+  |
|    - Lists     |  | Cards & Tables (13px text, 3px sharp borders)    |  |
|  - Attendance  |  |                                                  |  |
|  - Fees >      |  |                                                  |  |
|                |  +--------------------------------------------------+  |
+----------------+--------------------------------------------------------+
```
* **Sidebar Width**: `240px` fixed, with smooth collapse to `60px` icon-only or hidden.
* **Content Wrapper**: Fluid responsive padding (`12px 16px`).

### Mobile / Tablet Layout ($< 992\text{px}$)
```
+-------------------------------------------------------------------------+
| [☰ TOGGLE] [SCHOOL LOGO & TITLE]                  [NOTIFICATION] [USER] |
+-------------------------------------------------------------------------+
|                                                                         |
|  PAGE CONTENT: Responsive fluid cards & horizontally scrollable tables  |
|                                                                         |
+-------------------------------------------------------------------------+
       | (When toggle clicked)
       v
+------------------+------------------------------------------------------+
| SIDEBAR DRAWER   |  SEMI-TRANSPARENT BACKDROP (.sidebar-backdrop)       |
| (Slides from L)  |  (Tap anywhere here to close sidebar)                |
+------------------+------------------------------------------------------+
```
* **Slide Drawer**: `260px` width, slides in smoothly (`transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1)`).
* **Backdrop**: Covers screen with `rgba(0, 0, 0, 0.45)` for fast intuitive touch dismissal.

---

## 4. Sidebar & Menu Specifications

### Main Navigation Structure
* **Brand Area**: Compact logo (`32px x 32px`) + School Name (`14px font-weight: 600`).
* **Menu Items**:
  * Height: `36px` per item.
  * Padding: `8px 12px`.
  * Left icon: `16px` width with fixed spacing.
  * Treeview toggle indicator: Small chevron rotated `90deg` when open.
  * Active item: Distinct crisp background (`#ffffff` on primary, or light navy tint) + `2px` left border accent.
* **Submenus (`.nav-treeview`)**:
  * Indented by `16px` with small dot or dash indicator.
  * Smooth accordion slide effect without page reloads.

---

## 5. UI Component Guidelines

### A. Cards (`.card`)
```css
.card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    margin-bottom: 12px;
}
.card-header {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 600;
}
.card-body {
    padding: 12px;
}
```

### B. Buttons (`.btn`)
```css
.btn {
    font-size: 12.5px;
    font-weight: 500;
    padding: 5px 12px;
    border-radius: 3px;
    transition: all 0.15s ease-in-out;
}
.btn-primary {
    background-color: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.btn-primary:hover {
    background-color: #001f3d;
    border-color: #001f3d;
}
```

### C. Tables (`.table`)
* Border: `1px solid #e2e8f0`.
* Header (`th`): `background: #f1f5f9; color: #334155; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 6px 10px;`.
* Cells (`td`): `padding: 6px 10px; font-size: 12.5px; color: #1e293b; border-top: 1px solid #f1f5f9;`.
* Table Striping: `#f8fafc` on alternating rows.

### D. Forms & Inputs (`.form-control`)
```css
.form-control {
    height: 32px;
    padding: 4px 8px;
    font-size: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background-color: #ffffff;
    color: #0f172a;
}
.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    outline: none;
}
```

---

## 6. JavaScript & Event Preservation Plan
* **No Broken Events**:
  - Keep `data-widget="pushmenu"` click listener to toggle `.sidebar-open` and `.sidebar-collapse`.
  - Add native lightweight accordion click handler for `.has-treeview > a` to slide-toggle `.nav-treeview` and toggle `.menu-open`.
  - Add click listener on `.sidebar-backdrop` to close sidebar on mobile.
  - Support existing DataTables, Select2, jQuery Validation, Toastr, DatePicker without conflict.
* **No `adminlte.js` Dependency**: All behaviors implemented in `< 50 lines` of clean, robust vanilla JS/jQuery (`arise-theme.js`).

---

## 7. Implementation Roadmap
1. Create `public/assets/school/css/arise-theme.css` containing complete compact, sharp, mobile-friendly styles (replacing the 1.56 MB `adminlte.min.css`).
2. Create `public/assets/school/js/arise-theme.js` containing lightweight pushmenu and treeview controllers (replacing `adminlte.js`).
3. Update `resources/views/layout/app.blade.php`, `resources/views/layout/header.blade.php`, and `resources/views/layout/sidebar.blade.php` to link the new theme and remove AdminLTE scripts/styles.
4. Verify all submenus, cards, forms, tables, modals, and mobile responsiveness.
