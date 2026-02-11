# Typography & UI/UX Upgrade Design Plan
## Liwonde Sun Hotel - Passalacqua-Inspired Design Enhancement

**Project:** Liwonde Sun Hotel Website (Rosalyns-hotel-2026)  
**Reference:** https://www.passalacqua.it/en/  
**Type:** Styling-only upgrade (no functionality changes)  
**Date:** 2026-02-11

---

## Executive Summary

This plan outlines a comprehensive typography and UI/UX upgrade to align the Liwonde Sun Hotel website with the editorial luxury aesthetic of Passalacqua. The project already has a foundation of Passalacqua-inspired styling; this plan refines and completes the implementation across all pages and components.

### Current State Assessment

**Already Implemented:**
- Editorial hero sections with multi-layer overlays
- Cormorant Garamond (serif) + Jost (sans-serif) typography pairing
- Warm cream/charcoal/gold color palette
- Section header management via database
- Dynamic theme color system via admin

**Gaps Identified:**
1. Inconsistent typography scale across components
2. Hero section styling needs refinement for all pages
3. Section spacing and visual hierarchy needs standardization
4. Footer styling needs Passalacqua-inspired refinement
5. Some components lack the editorial polish

---

## 1. Reference Analysis: Passalacqua Design Patterns

### 1.1 Typography Characteristics

| Element | Passalacqua Style | Implementation Notes |
|---------|------------------|---------------------|
| **Headings** | Cormorant Garamond, light weights (300-400), generous letter-spacing | Already using Cormorant Garamond - refine weights |
| **Body Text** | Clean sans-serif (Jost/Inter), 16-18px base, 1.6-1.8 line-height | Already using Jost - standardize sizes |
| **Editorial Labels** | Uppercase, wide letter-spacing (3-4px), small size (11-12px) | Need to add to section headers |
| **Accent Text** | Italic serif for quotes, captions | Partially implemented - expand usage |

### 1.2 Hero Section Design

**Passalacqua Hero Pattern:**
- Full-viewport height (100vh) on desktop
- Centered or left-aligned content with generous whitespace
- Multi-layer overlay system (vignette + gradient)
- Editorial meta information (date, issue, category)
- Large serif headline (48-72px desktop)
- Subtle scroll indicator

**Current Implementation:**
- Editorial hero component exists in `includes/hero.php`
- Multi-layer overlays implemented
- Video/image background support
- **Needs:** Typography refinement, spacing adjustments

### 1.3 Section Styling Patterns

**Passalacqua Section Characteristics:**
- Generous vertical padding (80-120px)
- Cream backgrounds (#FAF8F5, #F5F0EB)
- Subtle dividers (2px gold gradient)
- Editorial labels above titles
- Consistent spacing rhythm (8px base unit)

**Current Implementation:**
- Many editorial section classes exist
- **Needs:** Standardization across all sections

### 1.4 Footer Design

**Passalacqua Footer Pattern:**
- Dark charcoal background (#1A1A1A)
- Multi-column layout with generous spacing
- Small uppercase labels (11px, 3px letter-spacing)
- Subtle gold accent lines
- Minimal social icons (outlined style)

**Current Implementation:**
- Minimalist footer exists in `css/footer.css`
- **Needs:** Refinement to match Passalacqua editorial style

---

## 2. Typography Scale & Hierarchy

### 2.1 Font Families (Already Configured)

```css
--font-serif: 'Cormorant Garamond', Georgia, 'Times New Roman', serif;
--font-sans: 'Jost', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

### 2.2 Typography Scale (To Be Standardized)

| Element | Font Family | Size | Weight | Line Height | Letter Spacing | Use Case |
|---------|-------------|------|--------|-------------|----------------|----------|
| **H1 - Hero Title** | Serif | 64px | 300 | 1.1 | -0.02em | Page hero headings |
| **H2 - Section Title** | Serif | 48px | 400 | 1.2 | -0.01em | Main section headings |
| **H3 - Card Title** | Serif | 32px | 400 | 1.2 | 0 | Card/feature titles |
| **H4 - Subtitle** | Serif | 24px | 400 | 1.3 | 0 | Subsection titles |
| **Editorial Label** | Sans | 11px | 500 | 1.4 | 3px | Section labels (uppercase) |
| **Body Large** | Sans | 18px | 400 | 1.7 | 0.01em | Lead paragraphs |
| **Body Base** | Sans | 16px | 400 | 1.7 | 0 | Standard body text |
| **Body Small** | Sans | 14px | 400 | 1.6 | 0 | Captions, meta info |
| **Nav Link** | Sans | 13px | 400 | 1 | 2px | Navigation (uppercase) |
| **Button** | Sans | 14px | 600 | 1 | 1.2px | CTA buttons |
| **Quote** | Serif Italic | 28px | 300 | 1.4 | 0.01em | Testimonial quotes |

### 2.3 Responsive Typography Scale

| Breakpoint | Scale Factor | H1 | H2 | H3 | Body |
|------------|--------------|----|----|----|----|
| Desktop (1500px+) | 100% | 64px | 48px | 32px | 16px |
| Tablet (768-1499px) | 85% | 54px | 40px | 28px | 16px |
| Mobile (<768px) | 70% | 42px | 32px | 24px | 16px |

---

## 3. Color Palette (Already Configured)

### 3.1 Primary Colors

```css
/* Deep Charcoal (Primary) */
--navy: #1A1A1A;
--deep-navy: #111111;
--charcoal: #1A1A1A;

/* Warm Bronze/Gold (Accent) */
--gold: #8B7355;
--dark-gold: #6B5740;
--accent-color: #8B7355;

/* Warm Cream Backgrounds */
--cream: #F5F0EB;
--cream-light: #FAF8F5;
--cream-dark: #EDE7E0;
```

### 3.2 Color Usage Guidelines

| Element | Color | Context |
|---------|-------|---------|
| **Primary Text** | #1A1A1A | Headings, body text |
| **Secondary Text** | #6B5740 | Descriptions, meta info |
| **Accent Text** | #8B7355 | Links, highlights |
| **Section Background** | #FAF8F5 | Most section backgrounds |
| **Card Background** | #FFFFFF | Card backgrounds |
| **Footer Background** | #1A1A1A | Footer area |
| **Dividers** | #8B7355 (gradient) | Section dividers |

---

## 4. Hero Section Specifications

### 4.1 Editorial Hero Component

**File:** `includes/hero.php` (already exists)

**Required CSS Updates:**

```css
/* Editorial Hero Section - Refined */
.editorial-hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.editorial-hero-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
}

.editorial-hero-overlay--base {
    background: rgba(26, 26, 26, 0.3);
}

.editorial-hero-overlay--vignette {
    background: radial-gradient(ellipse at center, transparent 0%, rgba(0,0,0,0.4) 100%);
}

.editorial-hero-overlay--gradient {
    background: linear-gradient(180deg, rgba(26,26,26,0.2) 0%, rgba(26,26,26,0.5) 100%);
}

.editorial-hero-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    font-family: var(--font-sans);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: rgba(250, 248, 245, 0.8);
}

.editorial-hero-title {
    font-family: var(--font-serif);
    font-size: 64px;
    font-weight: 300;
    line-height: 1.1;
    color: #FAF8F5;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
}

.editorial-hero-lead {
    font-family: var(--font-sans);
    font-size: 18px;
    line-height: 1.7;
    color: rgba(250, 248, 245, 0.9);
    max-width: 600px;
}
```

### 4.2 Hero Pages Requiring Updates

| Page | Current Status | Required Changes |
|------|---------------|------------------|
| index.php | Uses hero slides | Refine typography, add editorial meta |
| room.php | Custom hero | Standardize with editorial component |
| restaurant.php | Uses page_heroes | Typography refinement |
| events.php | Uses page_heroes | Typography refinement |
| gym.php | Uses page_heroes | Typography refinement |
| conference.php | Uses page_heroes | Typography refinement |

---

## 5. Section Styling Specifications

### 5.1 Section Header Component

**File:** `includes/section-headers.php` (already exists)

**Required CSS:**

```css
/* Section Header - Editorial Style */
.section-header {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 48px auto;
    padding: 0 20px;
}

.section-label {
    display: block;
    font-family: var(--font-sans);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 16px;
}

.section-subtitle {
    font-family: var(--font-serif);
    font-size: 20px;
    font-weight: 400;
    color: var(--dark-gold);
    margin-bottom: 12px;
    font-style: italic;
}

.section-title {
    font-family: var(--font-serif);
    font-size: 48px;
    font-weight: 400;
    line-height: 1.2;
    color: var(--navy);
    margin-bottom: 16px;
}

.section-description {
    font-family: var(--font-sans);
    font-size: 16px;
    line-height: 1.7;
    color: var(--dark-gold);
    max-width: 600px;
    margin: 0 auto;
}
```

### 5.2 Section Spacing Standard

```css
/* Standard Section Padding */
.editorial-section {
    padding: 100px 0;
    background: var(--cream-light);
}

.editorial-section--alt {
    padding: 100px 0;
    background: #FFFFFF;
}

.editorial-section--compact {
    padding: 60px 0;
}
```

### 5.3 Section Divider

```css
/* Editorial Divider */
.editorial-divider {
    width: 60px;
    height: 2px;
    background: linear-gradient(90deg, var(--gold) 60%, var(--cream-light) 100%);
    margin: 24px auto;
    border-radius: 1px;
}
```

---

## 6. Footer Specifications

### 6.1 Footer Structure

**File:** `includes/footer.php` (already exists)

**Required CSS Updates:**

```css
/* Editorial Footer - Passalacqua Style */
.minimalist-footer {
    background: var(--charcoal);
    padding: 80px 0 40px;
    position: relative;
}

.minimalist-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(139, 115, 85, 0.3);
}

.minimalist-footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 48px;
    margin-bottom: 48px;
}

.minimalist-footer-column h4 {
    font-family: var(--font-sans);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 24px;
}

.minimalist-footer-links a {
    font-family: var(--font-sans);
    font-size: 14px;
    color: rgba(250, 248, 245, 0.7);
    text-decoration: none;
    transition: color 0.3s ease;
    display: block;
    padding: 6px 0;
}

.minimalist-footer-links a:hover {
    color: var(--gold);
}

.minimalist-footer-bottom {
    border-top: 1px solid rgba(139, 115, 85, 0.2);
    padding-top: 24px;
    text-align: center;
    font-family: var(--font-sans);
    font-size: 13px;
    color: rgba(250, 248, 245, 0.5);
}
```

---

## 7. Component Styling Updates

### 7.1 Card Components

**Rooms, Events, Conference Cards:**

```css
/* Editorial Card Base */
.editorial-card {
    background: #FFFFFF;
    border-radius: 0;
    box-shadow: 0 8px 32px rgba(40, 40, 40, 0.08);
    overflow: hidden;
    transition: box-shadow 0.4s ease, transform 0.4s ease;
}

.editorial-card:hover {
    box-shadow: 0 16px 48px rgba(40, 40, 40, 0.13);
    transform: translateY(-4px);
}

.editorial-card-title {
    font-family: var(--font-serif);
    font-size: 32px;
    font-weight: 400;
    line-height: 1.2;
    color: var(--navy);
    margin-bottom: 12px;
}

.editorial-card-meta {
    font-family: var(--font-sans);
    font-size: 14px;
    color: var(--dark-gold);
    margin-bottom: 16px;
}
```

### 7.2 Button Components

```css
/* Editorial Buttons */
.editorial-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: linear-gradient(135deg, var(--gold) 0%, #C9A84C 100%);
    color: #FAF8F5;
    font-family: var(--font-sans);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.editorial-btn-primary:hover {
    background: linear-gradient(135deg, #C9A84C 0%, var(--gold) 100%);
    box-shadow: 0 12px 36px rgba(139, 115, 85, 0.25);
    transform: translateY(-2px);
}

.editorial-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: transparent;
    color: var(--gold);
    font-family: var(--font-sans);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    border: 1px solid var(--gold);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.editorial-btn-secondary:hover {
    background: var(--gold);
    color: #FAF8F5;
}
```

---

## 8. File-by-File Implementation Plan

### 8.1 CSS Files to Modify

| File | Changes Required |
|------|------------------|
| `css/style.css` | Add typography scale, refine editorial sections, standardize spacing |
| `css/header.css` | Refine navigation typography, ensure Passalacqua alignment |
| `css/footer.css` | Update footer styling to match editorial style |
| `css/theme-dynamic.php` | Already configured - no changes needed |

### 8.2 PHP Files to Review

| File | Changes Required |
|------|------------------|
| `includes/hero.php` | Already has editorial structure - may need HTML class updates |
| `includes/section-headers.php` | Already exists - ensure CSS classes match |
| `includes/footer.php` | Review HTML structure for CSS compatibility |
| `index.php` | Ensure all sections use editorial classes |
| `room.php` | Standardize hero and section styling |
| `restaurant.php` | Standardize hero and section styling |
| `events.php` | Standardize hero and section styling |
| `gym.php` | Standardize hero and section styling |
| `conference.php` | Standardize hero and section styling |

---

## 9. Database Compatibility

### 9.1 Existing Tables (No Changes Required)

The following tables already support the design system:

- **page_heroes** - Hero content per page
- **section_headers** - Section titles and descriptions
- **site_settings** - Theme colors and settings

### 9.2 Admin Theme Management

**File:** `admin/theme-management.php` (already exists)

The admin panel already supports:
- Color customization (navy, gold, accent colors)
- Preset themes including 'passalacqua-classic'
- Cache clearing for applying changes

**No database changes required.**

---

## 10. Implementation Checklist

### Phase 1: Typography Foundation
- [ ] Add typography scale CSS variables to `css/style.css`
- [ ] Update all heading styles (H1-H4) with Passalacqua specs
- [ ] Standardize body text sizes and line heights
- [ ] Add editorial label style (11px, 3px letter-spacing)
- [ ] Update navigation link typography

### Phase 2: Hero Sections
- [ ] Refine `.editorial-hero` CSS for all pages
- [ ] Update hero title typography (64px, weight 300)
- [ ] Refine hero meta information styling
- [ ] Standardize hero overlay system
- [ ] Update scroll indicator styling

### Phase 3: Section Styling
- [ ] Standardize section padding (100px vertical)
- [ ] Update `.section-header` component styling
- [ ] Add editorial divider component
- [ ] Refine section background colors
- [ ] Update all section-specific classes

### Phase 4: Cards & Components
- [ ] Refine `.editorial-card` base styles
- [ ] Update room card styling
- [ ] Update event card styling
- [ ] Update conference card styling
- [ ] Update facility card styling
- [ ] Standardize button components

### Phase 5: Footer
- [ ] Update `.minimalist-footer` styling
- [ ] Refine footer column headings
- [ ] Update footer link styling
- [ ] Refine footer social icons
- [ ] Update footer copyright area

### Phase 6: Responsive Adjustments
- [ ] Add responsive typography scale
- [ ] Update mobile hero sections
- [ ] Refine mobile section padding
- [ ] Update mobile card layouts
- [ ] Test all breakpoints

### Phase 7: Page-Specific Updates
- [ ] Review and update index.php sections
- [ ] Review and update room.php styling
- [ ] Review and update restaurant.php styling
- [ ] Review and update events.php styling
- [ ] Review and update gym.php styling
- [ ] Review and update conference.php styling

### Phase 8: Final Polish
- [ ] Cross-browser testing
- [ ] Performance optimization
- [ ] Accessibility audit
- [ ] Admin theme management verification
- [ ] Documentation updates

---

## 11. Design Principles Summary

### 11.1 Core Passalacqua Principles

1. **Generous Whitespace** - Allow content to breathe with 80-120px section padding
2. **Editorial Typography** - Serif headlines with light weights, sans-serif body
3. **Subtle Color Palette** - Warm creams, charcoal, and bronze/gold accents
4. **Minimal Dividers** - 2px gradient lines instead of heavy borders
5. **Refined Interactions** - Subtle hover states with smooth transitions

### 11.2 Spacing System (8px Base Unit)

| Unit | Value | Use Case |
|------|-------|----------|
| 1x | 8px | Small gaps, icon spacing |
| 2x | 16px | Card padding, small margins |
| 3x | 24px | Medium margins, button padding |
| 4x | 32px | Large margins, section gaps |
| 6x | 48px | Component spacing |
| 8x | 64px | Section spacing |
| 12x | 100px | Full section padding |

---

## 12. Success Criteria

The upgrade will be considered successful when:

1. ✅ All typography follows the documented scale
2. ✅ Hero sections match Passalacqua editorial style
3. ✅ Section spacing is consistent (100px standard)
4. ✅ Footer matches Passalacqua minimalist style
5. ✅ All pages use consistent editorial components
6. ✅ Admin theme management continues to work
7. ✅ Responsive design maintained across all breakpoints
8. ✅ No functionality changes (styling only)

---

## Appendix A: CSS Variable Reference

```css
:root {
    /* Typography */
    --font-serif: 'Cormorant Garamond', Georgia, 'Times New Roman', serif;
    --font-sans: 'Jost', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    
    /* Colors */
    --navy: #1A1A1A;
    --deep-navy: #111111;
    --gold: #8B7355;
    --dark-gold: #6B5740;
    --cream: #F5F0EB;
    --cream-light: #FAF8F5;
    --cream-dark: #EDE7E0;
    --charcoal: #1A1A1A;
    
    /* Shadows */
    --shadow-subtle: 0 1px 3px rgba(0, 0, 0, 0.04);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.10);
    
    /* Transitions */
    --transition-base: 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
    
    /* Border Radius */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-full: 9999px;
}
```

---

**Document Version:** 1.0  
**Last Updated:** 2026-02-11  
**Status:** Ready for Implementation
