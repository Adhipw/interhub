# InternHub Design System 2026

## 1. Vision & Principles
InternHub's Design System is built to feel **Premium, Sophisticated, and Human-made**. It avoids the generic "AI-generated" look by prioritizing organic spacing, professional typography, and high-fidelity interaction details.

### Principles:
- **World-Class Quality**: Every pixel matters.
- **Sophisticated Professionalism**: Clean lines, deep colors, and subtle elevations.
- **Youth-Friendly**: Modern, but not "childish" or "neon-heavy".
- **Accessible & Responsive**: Inclusive by design.

---

## 2. Design Tokens

### Colors (Semantic)
| Token | Use Case |
|-------|----------|
| `primary-600` | Main Brand Color, Primary Actions |
| `accent-500` | Highlights, Special Features |
| `neutral-50` | Backgrounds (Light) |
| `neutral-900` | Main Text (Light Mode) |
| `success` | Positive status, acceptance |
| `danger` | Destructive actions, rejection |

### Typography Scale
- **Display**: `Outfit` (Bold, Tracking -0.02em)
- **Body**: `Inter` (Medium/Regular)

### Radius Scale
- `xs`: 4px
- `sm`: 8px
- `md`: 12px (Small Cards)
- `lg`: 16px (Buttons, Inputs)
- `xl`: 24px (Standard Cards)
- `2xl`: 32px (Large Shells)

---

## 3. Components Foundation

### Buttons
- **Primary**: Solid primary with a soft shadow.
- **Secondary**: Neutral background for low-priority actions.
- **Accent**: Reserved for "Premium" or "New" features.
- **Interactions**: Subtle `scale-98` on click to feel tactile.

### Inputs
- **Radius**: 16px (`rounded-2xl`).
- **Focus State**: Ring 4px with 10% opacity primary color.
- **Typography**: Label is uppercase, bold, and tracked for high-fidelity feel.

---

## 4. Layout Architecture

### Public Layout
- Clean top navigation with glassmorphism on scroll.
- Professional footer with structured information hierarchy.

### Auth Layout
- Split-screen design to build trust via visual marketing on the left.
- Centered auth card for focus on the right.

### Dashboard Shell
- Collapsible modern sidebar with 32px corner radius for active state indicators.
- Glassmorphic topbar for modern enterprise look.
