# BLVKDOT Documentation

Welcome to the BLVKDOT documentation folder. Here you’ll find everything you need for seamless development, onboarding, and maintenance—including product prompts, UI/UX and backend blueprints, copy decks, architecture, API specs, testing strategy, workflows, compliance, and localization.

## How to Use These Files

- Reference the right file for your context:

## Structure

- **Coding Guidance for BLVKDOT (Piece by Piece).md** — Guidance for using this documentation.
- **prompt.md** — Product requirements and feature prompt.
- **blvkdot_ui_expanded_build_prompt.md** — UI/UX, flows, and component mapping.
- **blvkdot_backend_expanded_build_prompt.md** — Backend, API, and admin mapping.
- **en.json** — Copy deck.
- **architecture/** — Diagrams and architecture guides.
- **api/** — OpenAPI docs, Postman collection, API practices.
- **testing/** — Test plans and coverage.
- **workflows/** — Git, code review, release process.
- **compliance/** — Legal, privacy, consent, audit.
- **localization/** — i18n instructions and language decks.

Coding Guidance for BLVKDOT (Piece by Piece)

Welcome to the BLVKDOT codebase! This documentation folder houses all the master prompts and reference specs needed for Copilot, contributors, and maintainers.


  - UI/Frontend: `blvkdot_ui_expanded_build_prompt.md`
  - Backend: `blvkdot_backend_expanded_build_prompt.md`
  - Product: `prompt.md`
  - Copy: `en.json`
- When coding, you should:
  - Follow flows, endpoints, and structures in the prompts.
  - Use message keys from `en.json` for all text.
  - Respect feature toggling, RBAC, compliance.
  - Use modular, pluggable architecture for advanced modules.
- If unsure, consult the relevant prompt in `/documentation` first.
- Update these files as requirements evolve.

## File Structure

- `/documentation/prompt.md`
- `/documentation/blvkdot_ui_expanded_build_prompt.md`
- `/documentation/blvkdot_backend_expanded_build_prompt.md`
- `/documentation/en.json`
- `/documentation/Copilot Coding Guidance for BLVKDOT (Piece by Piece).md`
- `/documentation/architecture/`
- `/documentation/api/`
- `/documentation/testing/`
- `/documentation/workflows/`
- `/documentation/compliance/`
- `/documentation/localization/`



1. Read the relevant expanded prompt before coding a feature.
2. For all UI/app text, use keys in `en.json`.
3. All new flows/components/endpoints must align with the mapped structure in the prompt.
4. Advanced modules: plug-in style, admin-toggable.
5. Admin God-Mode: always enforce RBAC and audit logging.
6. Document new endpoints/components referencing the prompt.
7. If in doubt, check `prompt.md` and related prompts.

---

_Copilot and all contributors must use these documentation files as the single source of truth for BLVKDOT piece-by-piece development._

> **Tip:** Start with the Coding Guidance file for how to use these docs.