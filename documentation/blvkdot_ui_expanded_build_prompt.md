# BLVKDOT WEB UI – COMPLETE EXPANDED BUILD PROMPT

## Purpose
Defines all UI/UX, flows, atomic components, states, and design systems for the BLVKDOT web app.

## Table of Contents
- Principles & Branding
- Pages & Navigation
- Components (Cards, Forms, Modals, Lists, etc.)
- User Flows (Onboarding, Wallet, Booking, Challenge, etc.)
- Admin/Agent/Attendant UI
- Advanced Modules
- State Variants
- Style System/Design Tokens
- Testing & QA
- Delivery Instructions

---

## 1. GLOBAL PRINCIPLES

- **Visual Identity:** Dark urban theme, neon green/accents, street/Naija urban inspired. Branded “One Shot. One King.”
- **Responsiveness:** Fully functional on desktop, tablet, mobile web.
- **Accessibility:** Keyboard navigation, screen reader-ready, color contrast, text resizing.
- **i18n:** All text content from `en.json` (Pidgin/English), localization-ready.
- **Componentization:** All UI elements atomized for reuse (cards, forms, modals, lists).
- **State Management:** Centralized (Redux, Zustand, etc.), with optimistic UI and error handling.
- **Performance:** Lazy load heavy assets. Quick load for all main flows.
- **Testing:** Storybook for all components, E2E for flows.

---

## 2. TOP-LEVEL PAGE ROUTES & NAVIGATION

- `/` – Home / Dashboard
- `/auth/login` – Login
- `/auth/signup` – Sign Up
- `/onboarding` – First-time User Flow
- `/wallet` – Wallet Dashboard
- `/wallet/history` – Transaction History
- `/booking` – Table Booking
- `/booking/confirm` – Booking Confirmation
- `/booking/qr` – QR Ticket
- `/challenge` – Start Challenge
- `/challenge/history` – Challenge History
- `/crew` – Crew/Crew Dashboard
- `/crew/war` – Crew War Flow
- `/tournaments` – Tournament List
- `/tournaments/:id` – Tournament Details
- `/leaderboard` – Leaderboard
- `/shotlab` – Practice/Shotlab
- `/feed` – Clips/Feed
- `/merch` – Merch Store
- `/support` – Support/FAQ
- `/settings` – User Settings (profile, a11y, data saver, etc.)
- `/admin` – Admin Dashboard (role-based)
- `/agent` – Agent/Operator Dashboard
- `/attendant` – Attendant Console
- `/legal` – Legal/Privacy/TOS

**Navigation:**  
- Sticky top header: App logo, notifications, quick links.
- Sidebar or bottom nav (mobile): Home, Wallet, Booking, Crew, Feed, Settings.
- Contextual breadcrumbs for deep flows.

---

## 3. UI COMPONENTS & ATOMS

### A. **Cards**
- UserCard (avatar, nickname, XP, badges, quick actions)
- WalletCard (balance, top-up, withdraw)
- BookingSlotCard (time, availability, surge/upsell)
- ChallengeCard (type, opponent, status, stake, badges)
- CrewCard (logo, name, join/status)
- TournamentCard (title, prize, join button, bracket link)
- ClipCard (video/thumb, poster, likes/comments)
- MerchCard (item, price, buy/add)
- NotificationCard (icon, message, timestamp)
- FeedCard (user, content, actions)
- DrillCard (name, locked/unlocked, stats)
- BadgeCard (icon, name, unlocked/locked)

### B. **Forms/Inputs**
- Auth forms (OTP, nickname, avatar picker)
- Wallet amount input (number pad, amount quick picks)
- Booking calendar/timeline picker
- Challenge stake input (slider or keypad)
- Crew creation/edit form
- Search bars (users, venues, events)
- Contact/support form

### C. **Modals & Overlays**
- Challenge modal (stake, insurance, confirm)
- Booking confirmation modal
- QR code modal (show/scan)
- Payment provider modal (Paystack/Flutterwave/Monnify)
- Clip share modal
- Report/flag modal
- Legal consent/age gate modal

### D. **Lists & Tables**
- Transaction list/table
- Leaderboard list/table (sortable)
- Crew/member list
- Tournament bracket
- Feed/clip list

### E. **Navigation Elements**
- Header (logo, nav links, notifications, profile dropdown)
- Sidebar (desktop), bottom nav (mobile)
- Breadcrumbs
- Stepper (onboarding, booking)
- Tabs (wallet/cash/points, crew feed/chat, etc.)

### F. **UI Feedback**
- Toasts (success, error, info, warning)
- Loaders (skeleton, spinner)
- Empty states (illustration, microcopy)
- Error fallback (retry, report)

### G. **Other**
- Avatar picker
- Badge/gallery viewer
- Audio cues (optional, subtle, toggleable)

---

## 4. CORE USER FLOWS (WIREFRAME MAPS)

### A. **Onboarding & Auth**
1. Splash → Welcome → OTP → Nickname → Avatar → Skill Calibration (guided drill) → Success → Dashboard

### B. **Wallet Flow**
1. Dashboard:  
   a. View balances (cash/points)  
   b. Top up (choose provider, enter amount, confirm, success/fail)  
   c. Withdraw (enter amount, select destination, confirm, success/fail)  
   d. View transaction history (list/table)

### C. **Booking Flow**
1. Calendar page:  
   a. Select date  
   b. See available slots (heatmap/timeline)  
   c. Pick slot, see price/upsells  
   d. Confirm (modal/summary)  
   e. Success → QR ticket  
   f. Waitlist/join if no slots

### D. **Challenge Flow**
1. Start challenge:  
   a. Pick opponent (search, leaderboard, recent)  
   b. Set stake/insurance  
   c. Confirm & send challenge  
   d. Opponent receives notification: accept/decline/counter  
   e. Both see status updates (pending, accepted, in-play, result, dispute)
   f. View challenge history

### E. **Crew Flow**
1. Crew dashboard:  
   a. View/join/create crew  
   b. See members, chat, crew feed  
   c. Start crew war (select crew, set terms, send)  
   d. Crew leaderboard  
   e. Crew perks/badges

### F. **Tournament Flow**
1. Tournament list:  
   a. See upcoming/ongoing  
   b. Register (if open)  
   c. View bracket  
   d. Play matches (see schedule)  
   e. Win/lose, see standings  
   f. View leaderboard/history

### G. **Feed & Clips Flow**
1. Feed page:  
   a. See latest clips, highlights, memes  
   b. Like, comment, share  
   c. Upload own clip (video picker, edit, post)  
   d. Report inappropriate content

### H. **ShotLab/Practice Flow**
1. Practice dashboard:  
   a. Pick drill (free/locked)  
   b. Complete drill (AR overlay if available)  
   c. Get feedback/stats  
   d. Leaderboard for drills

### I. **Merch Store Flow**
1. Storefront:  
   a. Browse items  
   b. View details  
   c. Add to cart/buy  
   d. Checkout (wallet/cash/card)  
   e. Order status/history

### J. **Support & Legal**
1. Support:  
   a. FAQ, search  
   b. Contact form/chat  
   c. Report problem  
2. Legal:  
   a. TOS, Privacy, Age Gate modals/pages

### K. **Settings**
1. Profile:  
   a. Edit info, change avatar  
   b. Security (2FA, sessions)  
   c. Accessibility (a11y, data saver, text size)  
   d. Data export/delete

---

## 5. ADMIN/AGENT/ATTENDANT FLOWS

- **Admin:** User mgmt, venue mgmt, booking override, content CMS, analytics, dispute resolution, moderation, payment audit, feature toggles.
- **Agent:** Bulk bookings, float management, venue-level transactions.
- **Attendant:** Table status grid, check-in/out, QR scan, manual scoring, payout control, dispute trigger.

---

## 6. ADVANCED MODULES (PLUGGABLE)

- **AI/ML Feed:** Personalized dashboard feed component, recommended matches/tournaments, opponent suggestions.  
- **Multi-currency:** Currency picker, rates display, currency-aware forms/receipts.
- **Insurance/Escrow:** Badge/notice in challenge/booking flows, partner logo, claim status.
- **Crowdfunding:** Venue/project explorer, invest modal, rewards dashboard.
- **Web3:** NFT badge viewer, connect wallet modal, on-chain match history badge.
- **Parental/Underage:** Age gate modal, restricted feature overlays, guardian dashboard.
- **Data Saver:** Toggle in settings, UI badge when active, low-data assets.

---

## 7. COMPONENT STATE VARIANTS

- All components must have:  
  - Default  
  - Loading  
  - Error  
  - Empty  
  - Success  
  - Disabled  
  - Hover/focus  
  - Mobile/desktop responsive

---

## 8. STYLE SYSTEM & DESIGN TOKENS

- **Colors:**  
  - Background: #0A0A0B  
  - Cards: #121214  
  - Primary: #00FF66  
  - Danger: #FF3B3B  
  - Accent: #FFB86B  
  - Gold: #FFD700  
  - Text: #FFF, #B3B3B3
- **Typography:**  
  - Headline: Supercell-Magic / Anton  
  - Body: Inter  
  - Numbers: Roboto Mono
- **Elevation, borders, spacing, shadows** as per Figma/design system.

---

## 9. TESTING & QA

- **Storybook coverage:** All atoms/molecules/organisms
- **E2E Flows:** Onboarding, wallet, booking, challenge, crew, tournament, merch, support
- **A11y:** Automated and manual checks
- **Edge cases:** Mobile web, slow/spotty network, data saver, underage mode

---

## 10. DELIVERY INSTRUCTIONS

- Build UI story by story, starting from atomic components up to full pages and flows.
- All text from `en.json` copy deck.
- All business logic and states must be mapped to UI with clear feedback.
- Use placeholder/mock data for APIs first, then connect to backend.
- Conduct regular UX reviews with real Naija users for authenticity.

---
<!-- Fill in with detailed UI mapping, referencing en.json for all copy. -->