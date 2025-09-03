# BLVKDOT WEB BACKEND – COMPLETE EXPANDED BUILD PROMPT

## Purpose
Blueprint for all backend services, APIs, admin/attendant/agent control, and God-mode admin for BLVKDOT.

## Table of Contents
- Global Principles & Security
- API Modules & Flows
- Admin/Agent/Attendant Functions
- API Endpoint Examples
- System Integrations
- Data Model/Export
- Compliance
- Monitoring & Maintenance
- Delivery Instructions

---

## 1. GLOBAL PRINCIPLES

- **API-First:** All business logic must be exposed via RESTful (and optionally GraphQL) APIs.
- **Modularity:** Every functional domain (auth, wallet, booking, crew, tournaments, etc.) is a module/service, decoupled and pluggable.
- **Admin God-Mode:** Super-admin controls to toggle/patch features live, manage API keys/integrations, impersonate users, override any data, and access full audit logs.
- **Security:** JWT or OAuth2 auth, RBAC (role-based access control), audit logging, rate limiting, fraud detection, multi-region compliance.
- **Observability:** API status, logs, error tracking, performance metrics, real-time dashboards.
- **Localization:** All user-facing messages, notifications, and errors are i18n-ready.
- **Data Export:** Every entity (user, booking, transaction, etc.) can be exported (CSV, JSON), filtered by admin.
- **Testing:** Unit, integration, and E2E tests for all modules and flows.

---

## 2. CORE API MODULES & FLOWS

### A. **Authentication & Onboarding**
- Social/email/phone login, OTP, KYC upload/verification.
- Skill calibration endpoint (record onboarding drill stats).
- User profile CRUD, session/device management.

### B. **Wallet & Payments**
- Double-entry wallet/accounting, support for cash, points, and escrow.
- Providers: Paystack, Flutterwave, Monnify; dynamic provider choice.
- Fund, withdraw, transfer, transaction history, escrow for challenges.
- USSD/SMS endpoints for offline wallet operations.
- Fraud/anomaly detection hooks on all money flows.

### C. **Booking & Table Management**
- Venue, table, slot, and booking CRUD.
- Availability engine, waitlist, QR check-in/check-out.
- Dynamic pricing/surge logic for peak hours.
- Upsell (pro cue, merch, etc.) in booking flow.

### D. **Challenges & PvP**
- Challenge lifecycle: create, accept, decline, counter, expire, resolve, payout.
- Escrow and insurance for staked matches.
- Notifications/webhooks for all challenge state transitions.
- Dispute initiation, evidence upload, admin override.

### E. **Crew/Club Module**
- Crew CRUD, member invite/join/leave, pooled wallets.
- Crew wars (multi-user challenge), leaderboard, chat/messages.
- Crew admin controls, branding, content moderation.

### F. **Tournaments**
- Tournament CRUD, registration, bracket/seeding, match recording, payouts.
- Live bracket API, leaderboard, schedule, notifications.
- Admin override for results, disqualifications.

### G. **ShotLab/Practice**
- Drill CRUD, AR feedback data, paid drill unlock/purchase.
- Drill result submission, global/crew leaderboards.

### H. **Feed, Clips & Community**
- Feed post CRUD (text, image, video), clip moderation, reporting/flagging.
- Likes, comments, shares, abuse/NSFW AI moderation.
- Social graph endpoints (followers, crew, rivals).

### I. **Gamification**
- XP, level, badge CRUD; quest and season pass engine.
- Progress tracking, unlock logic, rewards payouts.

### J. **Marketplace & Merch**
- Product CRUD, cart, purchase, order, fulfillment.
- Admin manage inventory, pricing, promotions, featured items.

---

## 3. EXTENDED ADMIN (GOD-MODE) FUNCTIONS

- **Global Feature Toggles:**  
  - Enable/disable any module, flow, or experimental feature (per region, role, or globally).
- **API Key/Partner Mgmt:**  
  - CRUD for third-party API keys (payment, insurance, AI, etc.); rotate/invalidate keys; set/test integration endpoints.
- **Live Patch/Hotfix:**  
  - Change content, limits, copy, or rules without deploy (e.g. booking price, XP multiplier, promo banners).
- **User Impersonation:**  
  - Admin can “log in as” any user for support/debug.
- **Bulk Data Actions:**  
  - Export, import, mass-update, or delete users, bookings, transactions.
- **Content Moderation:**  
  - Review, approve, reject, or remove posts, clips, comments, and users.
- **Dispute/Abuse Resolution:**  
  - Access all reported disputes, upload/view evidence, resolve/escalate, ban/unban users.
- **Real-Time Dashboards:**  
  - System health, API performance, error logs, KPIs, fraud alerts, financial summaries.
- **Audit Logs:**  
  - Every admin action is logged (who, what, when, IP/device).
- **Compliance & Legal:**  
  - Download legal evidence bundles, respond to data requests, manage consent logs.
- **Localization:**  
  - Edit copy, translations, and Pidgin/English toggles in-app.

---

## 4. ATTENDANT/AGENT FUNCTIONS

- **Table Grid:**  
  - Real-time table/slot status, check-in/check-out users, override booking, manual score entry.
- **Float Management:**  
  - Manage cash/float for bookings, payouts, refunds.
- **Bulk/Offline Booking:**  
  - Book/confirm multiple slots for walk-ins or agent customers.
- **Dispute Trigger:**  
  - Escalate issues to admin; upload photos/evidence.
- **QR Scanner:**  
  - Scan user QR for entry, challenge, or payout.
- **Venue Analytics:**  
  - See daily/weekly venue stats, active bookings, revenue.

---

## 5. API ENDPOINTS (EXAMPLES)

### Auth:
- `POST /auth/login`
- `POST /auth/otp`
- `POST /auth/signup`
- `POST /auth/kyc`
- `GET /user/profile`
- `PATCH /user/profile`
- `POST /user/device`

### Wallet:
- `GET /wallet`
- `POST /wallet/fund`
- `POST /wallet/withdraw`
- `POST /wallet/transfer`
- `GET /wallet/history`
- `POST /wallet/ussd`
- `POST /wallet/escrow`
- `POST /wallet/insurance`

### Booking:
- `GET /venues`
- `GET /venues/:id/tables`
- `POST /booking`
- `GET /booking/:id`
- `POST /booking/waitlist`
- `POST /booking/checkin`
- `POST /booking/checkout`
- `GET /booking/qr/:id`

### Challenge:
- `POST /challenge`
- `POST /challenge/:id/accept`
- `POST /challenge/:id/decline`
- `POST /challenge/:id/counter`
- `POST /challenge/:id/result`
- `POST /challenge/:id/dispute`
- `GET /challenge/history`

### Crew:
- `POST /crew`
- `PATCH /crew/:id`
- `POST /crew/:id/join`
- `POST /crew/:id/war`
- `GET /crew/:id/leaderboard`
- `POST /crew/:id/message`

### Tournament:
- `GET /tournaments`
- `POST /tournaments`
- `POST /tournaments/:id/register`
- `POST /tournaments/:id/result`
- `GET /tournaments/:id/bracket`
- `GET /tournaments/:id/leaderboard`

### Feed/Clips:
- `POST /feed`
- `GET /feed`
- `POST /clip`
- `GET /clip`
- `POST /clip/:id/report`
- `POST /clip/:id/moderate`

### Gamification:
- `GET /xp`
- `POST /quest/complete`
- `GET /badges`
- `POST /seasonpass/activate`

### Marketplace:
- `GET /merch`
- `POST /cart`
- `POST /checkout`
- `GET /orders`
- `POST /promo/apply`

### Admin (God-Mode):
- `POST /admin/feature-toggle`
- `GET /admin/audit`
- `POST /admin/api-keys`
- `POST /admin/impersonate`
- `POST /admin/patch`
- `POST /admin/dispute/resolve`
- `POST /admin/content/moderate`
- `GET /admin/compliance/export`
- `POST /admin/localization`
- `GET /admin/dashboard`
- `GET /admin/fraud-alerts`

### Attendant/Agent:
- `GET /attendant/tables`
- `POST /attendant/checkin`
- `POST /attendant/score`
- `POST /attendant/float`
- `POST /attendant/bookings/bulk`
- `POST /attendant/dispute`

---

## 6. SYSTEM INTEGRATIONS

- **Payment:** Paystack, Flutterwave, Monnify – API key mgmt, failover, logs.
- **Insurance/Escrow:** Partner APIs for coverage, status, claims.
- **AI/ML:** Fraud, personalization, moderation endpoints.
- **Notification:** Push, SMS, email (Twilio, SendGrid, etc.).
- **Webhooks:** For real-time sync with partners/venues.
- **Analytics:** Event logging, funnel tracking, reporting (admin/venue/user).

---

## 7. DATA MODEL & EXPORT

- **Entities:** User, Wallet, Booking, Venue, Table, Crew, Challenge, Tournament, FeedPost, Clip, Transaction, Badge, Quest, SeasonPass, Merch, Order, APIKey, Dispute, AuditLog.
- **Data Export:** Admin can filter/export any entity (CSV/JSON), schedule regular backups.

---
PAYMENT SERVICE PROVIDERS

**Supported Payment Services**:
- **Paystack**
- **Flutterwave**
- **Monnify**

**Integration Instructions**:
- All wallet funding, withdrawals, and challenge payments must support Paystack, Flutterwave, and Monnify as selectable or fallback payment providers.
- Payment API selection can be dynamic (user preference, reliability, or failover).
- Each payment attempt must be logged with provider, status, and reference code.
- UI/UX: Clearly display provider name/logo during payment selection and confirmation.
- Admin dashboard: View, filter, and manage transactions by provider.

## 8. SECURITY & COMPLIANCE

- **Roles:** User, CrewAdmin, Attendant, Agent, SuperAdmin
- **RBAC:** Endpoint-level permissions, admin override.
- **Audit:** Immutable logs, legal evidence bundles (hash-stamped, optionally on-chain).
- **Consent:** All data/marketing/auth consents logged and exportable.
- **Data Requests:** Admin tool for search/export for subpoenas, user requests.

---

## 9. MONITORING & MAINTENANCE

- **API health, error logs, uptime, latency, key KPIs.**
- **Alerts for fraud, downtime, failed payments, integration issues.**
- **Admin system status dashboard with traffic, revenue, and incident logs.**

---

## 10. DELIVERY INSTRUCTIONS

- Build API and admin UI story by story, module by module.
- All endpoints must be documented (OpenAPI/Swagger).
- Feature toggles and God-mode admin tested for all flows.
- Connect to frontend via documented API contracts.
- Simulate all roles (admin, agent, attendant, user) in tests.
- Ensure localization, compliance, and security are always enforced.


<!-- Fill in with modular API definitions, RBAC, feature toggles, compliance, and reference to OpenAPI schema. -->