# Copilot Coding Guidance for BLVKDOT (Piece by Piece)

## 1. **Preparation**
- **Read the prompt.md file fully** to understand all modules, states, and requirements.
- **Set up your mono-repo structure:**  
  - `/frontend` (React Native/Next.js/Flutter, etc.)  
  - `/backend` (Laravel, Node, Django, etc.)  
  - `/shared` (types, contracts, constants)  
  - `/docs`, `/tests`, `/infra`, etc.

---

## 2. **Foundational Modules**

### a. **Scaffold Authentication & Onboarding**
- **Backend:**  
  - Scaffold User, Session, KYC models, and REST endpoints for:  
    - OTP send/verify, user create/update, KYC upload, profile.
- **Frontend:**  
  - Build splash, onboarding, OTP, signup, nickname/avatar, skill calibration screens.
  - Add inline validation, error/empty/loading states, a11y labels.
- **Test**: Register, login, KYC upload, onboarding flow.

---

### b. **Wallet & Payment Infrastructure**
- **Backend:**  
  - Double-entry Wallet model, transaction ledger, support for cash/points.
  - Integrate Paystack + at least 1 mobile money API (mock/test mode for now).
  - USSD/SMS endpoints (stub if not ready).
- **Frontend:**  
  - Wallet dashboard widget, top-up/withdraw screens, transaction history card.
  - Payment modal: support all payment types, bonus logic.
- **Test**: Top-up, withdraw, transfer, see history, handle failures.

---

### c. **Booking & Table Management**
- **Backend:**  
  - BookingSlot, Table models, availability, booking, waitlist, QR check-in endpoints.
- **Frontend:**  
  - Calendar picker, heatmap, booking confirmation, QR code ticket.
- **Test**: Book, cancel, check-in, join waitlist.

---

## 3. **Core Engagement Flows**

### a. **PvP & Crew Challenges**
- **Backend:**  
  - Challenge and CrewChallenge models, full lifecycle (create, accept, decline, counter, expire, resolve, payout), escrow logic, notifications.
- **Frontend:**  
  - Challenge modal (with stake, fee, insurance, badges), challenge list/history, accept/decline UI, crew challenge screens.
- **Test**: Challenge flow, escrow, payout, challenge history, all state transitions.

---

### b. **Crew/Club Module**
- **Backend:**  
  - Crew, CrewMember, CrewWallet, CrewWar models, pooled escrow, crew chat, crew leaderboard.
- **Frontend:**  
  - Crew dashboard, branding/banner, member mgmt, chat, crew war UI.
- **Test**: Create/join crew, crew war, crew wallet, chat, leaderboard.

---

### c. **Tournaments & Leaderboards**
- **Backend:**  
  - Tournament, Match, Bracket, Leaderboard models. Registration, bracket gen, match results.
- **Frontend:**  
  - Tournament list, bracket view, match cards, leaderboard widgets.
- **Test**: Register, play, advance, win, see standings.

---

### d. **ShotLab/Practice & Coach AI**
- **Backend:**  
  - Drill, DrillResult, ARData, paid drills API, leaderboard.
- **Frontend:**  
  - Drill cards, AR overlay UI, feedback, paid drill unlock, leaderboard.
- **Test**: Start/complete drills, AR display, buy paid drills.

---

## 4. **Cross-Cutting Features**

### a. **Notifications System**
- **Backend:**  
  - Push/SMS/email service integration, notification table, delivery logs.
- **Frontend:**  
  - Notification/toast UI, in-app inbox, push opt-in.
- **Test**: Trigger all notification types.

---

### b. **Admin, Attendant, Support, Agent Dashboards**
- Build for web (React, Vue, etc.)  
- **Admin:** Users, bookings, challenges, wallets, content, analytics, feature flags, logs.
- **Attendant:** Table grid, check-in, match control, payout, dispute.
- **Support:** Ticketing, chat, abuse/fraud review.
- **Agent:** Bulk booking, float management, offline submission.
- **Test**: Run through all admin/support/agent flows.

---

### c. **Community, Feed, Clips**
- **Backend:**  
  - FeedPost, Clip, Like, Comment models, video upload/editing API, abuse flagging.
- **Frontend:**  
  - Feed/clip UI, share to WhatsApp/IG, video editor, reporting.
- **Test**: Post, like, comment, share, report.

---

### d. **Gamification, Loyalty, Season Pass**
- **Backend:**  
  - XP, Level, Badge, Quest, SeasonPass models, achievement logic.
- **Frontend:**  
  - XP bars, badges, streaks, quest tracker, season pass UI.
- **Test**: Complete actions, earn XP, unlock badges.

---

### e. **Venue, Agent/Operator, Marketplace**
- **Backend:**  
  - Venue, Agent, Listing models, geo-mapping, live map API.
- **Frontend:**  
  - Venue discovery, map, agent dashboard, agent onboarding.
- **Test**: List/find venues, agent flows.

---

### f. **Mobile Money/USSD/Offline**
- **Backend:**  
  - Integrate/enable local wallet APIs, USSD, voucher redemption endpoints.
- **Frontend:**  
  - USSD/SMS modals, voucher entry.
- **Test**: Fund/withdraw, pay/receive via all channels.

---

## 5. **Integrations, Compliance, Analytics**

### a. **KYC, Privacy, Data Export**
- Build KYC flows, admin review, doc storage.
- Implement user data export/delete and admin retention controls.

### b. **Deep Analytics & Reporting**
- Backend events tracking, funnel API.
- Frontend dashboards for admin.

### c. **Localization/i18n**
- Implement i18n framework (start with English, plan for Pidgin, Hausa, Yoruba, Igbo).

---

## 6. **Testing & Automation**

- Write unit and E2E tests for each module as you finish.
- Use Copilot to generate test cases and QA automation scripts.
- Use the QA dashboard to track coverage and failures.

---

## 7. **Iterative Development Tips**
- **Build feature by feature:** Start with core flows (auth, wallet, booking, challenge), then expand.
- **Push/pull requests for each module:** Use clear branch names and commit messages.
- **Use Storybook (or similar) to document all UI states and variants.**
- **Refactor and test as you go; fix all errors and edge cases early.**
- **Regularly review the prompt.md to ensure all requirements are met.**

---

## 8. **Ready for Launch**
- All critical paths (booking, challenge, crew, wallet) must be fully testable and robust.
- All admin tools, notifications, compliance, analytics, and support flows must be live.
- Run final QA and UAT.
- Celebrate launch (“King dey!”).

---

**Ask Copilot for code in small, focused increments—one model, API, UI widget, or feature at a time.  
Always test each piece before moving to the next.  
Reference prompt.md often to ensure nothing is missing.**

---