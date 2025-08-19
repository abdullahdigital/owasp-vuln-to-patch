# 🦷 Dentist Appointment Booking System — Security Evolution (OWASP Top 10)

> **Author:** Abdullah  
> **Scope:** Full-stack, security-first capstone demonstrating **exploitation → mitigation → hardening** across **1 Frontend** and **3 Backends**  
> **Effort:** 20+ days of design, coding, exploitation, documentation, and video demos  
> **Repository Size:** **179 files** (tracked in this project as of Aug 18, 2025)

![status](https://img.shields.io/badge/status-active-brightgreen)
![stack](https://img.shields.io/badge/frontend-Svelte-informational)
![stack](https://img.shields.io/badge/backend-PHP%20%7C%20Secure%20PHP%20%7C%20Laravel-blue)
![security](https://img.shields.io/badge/OWASP-Top%2010-critical)
![license](https://img.shields.io/badge/license-MIT-lightgrey)

---

## 📊 Repository Stats (at commit time)

- **179 files total** (application code, configs, assets, tests).  
- Multi-app structure: **1 Frontend + 3 Backends**.  
- Timeboxed effort: **20+ days** from design → exploit → fix → harden → demo.

> *Exact file counts per folder may vary as development continues; the reported total reflects the working state during finalization of this README.*

---

## 🧱 Architecture at a Glance — **1 Frontend + 3 Backends**

This project is structured to demonstrate the progression from an insecure application to a fully hardened, enterprise-grade solution.

```text
dentist_app/
├── frontend/             # Svelte (UI/UX) — Single Frontend
│
├── backend/              # Plain PHP — two distinct versions for comparison
│   ├── insecure/         # Deliberately vulnerable PHP (OWASP Top 10)
│   └── secure/           # Fixed PHP version with security mitigations
│
├── laravel/              # Laravel API — a hardened, enterprise-grade backend
│
├── test/                 # Attack payloads, scripts, notes, and Proof-of-Concepts (PoCs)
│
├── LICENSE
└── README.md
```

### Why 3 Backends?

- **Backend #1 — Insecure PHP:**  
  Purpose: *Demonstrate* OWASP Top 10 by design.  
  Traits: raw SQL, no RBAC, weak auth, unsafe file handling, missing validation.

- **Backend #2 — Secure PHP (Mitigated):**  
  Purpose: *Prove fixes* within same language/runtime.  
  Traits: PDO prepared statements, CSRF tokens, output encoding, RBAC, strict file checks, better configs.

- **Backend #3 — Laravel (Hardened API):**  
  Purpose: *Showcase framework-driven security*.  
  Traits: Eloquent (parameterization by default), policies/gates, hashed passwords, CSRF, validation rules, rate limiting, queues/logging, .env secrets.

---

## 🧪 Security Journey — **7 Phases / Modules**

1. **Svelte Frontend (UI/UX)**  
   - Built modern, responsive interfaces (Home, About, Contact, Auth, Admin, Booking, History).  
   - Integrated forms and flows to trigger/observe both vulnerable and fixed server behaviors.

2. **Insecure PHP Backend (OWASP Top 10)**  
   - Implemented endpoints with **intentional flaws** to make exploitation reproducible and educational.

3. **Exploitation (All OWASP Top 10)**  
   - Executed attacks via Burp Suite & crafted payloads.  
   - Captured **successful exploit evidence** (screens, notes, repeatable steps).

4. **Secure PHP Backend (Mitigation Layer)**  
   - Re-implemented the same endpoints with **concrete defenses**.  
   - Ensured **same UX** while **blocking previous attacks**.

5. **Hardened Laravel Backend**  
   - Migrated critical flows to **Laravel** patterns (validation, auth, RBAC, storage, logging).  
   - Added guardrails: rate limiting, CSRF, policies, resource transformers.

6. **Video Demos**  
   - Recorded **before/after clips** of attacks vs. fixes for each OWASP category.

7. **Documentation & Reports**  
   - This README + structured notes, slides outlines, and runbooks under `test/`.

---

## 🛡️ OWASP Top 10 — Covered End-to-End

| # | Category                                   | Insecure PHP (Demonstrated)                                | Secure PHP (Mitigated)                                   | Laravel (Hardened)                                   |
|---|--------------------------------------------|-------------------------------------------------------------|-----------------------------------------------------------|------------------------------------------------------|
| 1 | Injection                                  | Raw SQL via string concat                                   | PDO prepared statements                                   | Eloquent/Query Builder (param by default)            |
| 2 | Broken Authentication                      | Plaintext passwords, weak sessions                          | Password hashing, session hardening                       | Hashing/guards, throttle, optional 2FA               |
| 3 | Sensitive Data Exposure                     | Data in plaintext, verbose errors                           | Transport + storage considerations, minimal error detail  | Config-driven encryption, .env secrets               |
| 4 | XML External Entities (XXE)                 | XML parsing w/ external entities enabled                     | Disable external entity resolution                         | Framework parsers, explicit disallow                 |
| 5 | Broken Access Control                      | No RBAC; IDOR in admin endpoints                            | Role checks, server-side enforcement                      | Policies/Gates, middleware authorization             |
| 6 | Security Misconfiguration                  | Debug on prod, default creds, directory listing              | Strict configs, env separation                             | .env separation, config:cache, debug off             |
| 7 | XSS (Cross-Site Scripting)                 | Directly reflecting unsanitized input                       | Output encoding, templating hygiene                       | Blade escaping, validation, CSP-ready                |
| 8 | Insecure Deserialization                   | Untrusted object handling                                   | Safe formats, integrity checks                             | Serialization guards, signed payloads                |
| 9 | Components w/ Known Vulnerabilities        | Outdated libs                                                | Version pinning, advisories monitoring                     | Composer/npm audit; upgrades                         |
|10 | Insufficient Logging & Monitoring          | Minimal logs, no alerts                                     | Structured logs, sensitive-field redaction                 | Centralized logs, handlers, rate limits, audit trail |

> ✅ Each category has **exploits** in `test/` and **mitigations** in `backend/secure/` + `laravel/`.

---

## 🧩 Feature Matrix (Functional)

| Area                  | Frontend (Svelte) | Insecure PHP | Secure PHP | Laravel |
|-----------------------|-------------------|--------------|------------|---------|
| Auth (login/signup)   | ✅                 | ✅            | ✅          | ✅       |
| Appointments (CRUD)   | ✅                 | ✅            | ✅          | ✅       |
| Admin Dashboard       | ✅                 | ✅            | ✅          | ✅       |
| File Uploads          | ✅                 | ✅ (unsafe)   | ✅ (safe)   | ✅ (safe)|
| Logs                  | UI views           | Minimal       | Structured | Centralized |
| Validation            | Client-side        | Weak/None     | Server-side| FormRequest |
| Access Control        | UI gating          | None/Bypass   | RBAC       | Policies/Gates |

---

## 🗂 Directory Highlights

- **`frontend/`** — Svelte + Vite + Tailwind (componentized UI, forms, routing).
- **`backend/insecure/`** — Deliberately vulnerable endpoints demonstrating each OWASP item.
- **`backend/secure/`** — Same endpoints, **fixed** with context-appropriate protections.
- **`laravel/`** — Production-style API with controllers, policies, middleware, migrations, seeders.
- **`test/`** — Attack payloads, Burp notes, manual steps, before/after evidence.
- **`database/`** — SQLite DB files and seeds for quick setup/reset.

---

## ⚙️ Setup & Run

> **Requirements**  
> - PHP >= 8.1, Composer  
> - Node.js >= 16, npm  
> - SQLite (bundled)  
> - Optional: php-curl, php-xml for specific demos

### 1) Frontend (Svelte)
```bash
cd frontend
npm install
npm run dev
# default: http://localhost:5173
```
### 2) Backend #1 — Insecure PHP (for Exploits)
```bash
cd backend/insecure
# Example: start built-in PHP server
php -S 127.0.0.1:8081 -t public
# API base: http://127.0.0.1:8081
```
### 3) Backend #2 — Secure PHP (Mitigations)
```bash
cd frontend
cd backend/secure
php -S 127.0.0.1:8082 -t public
# API base: http://127.0.0.1:8082
```
### 4) Backend #3 — Laravel (Hardened API)
```bash
cd laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --port=8000
# API base: http://127.0.0.1:8000
```
## 📝 What I (Abdullah) Did — Summary of Work

- 🔐 Planned and delivered a **7-phase security evolution** from *insecure → mitigated → hardened*.  
- 🎨 Built a modern **Svelte frontend** with realistic flows to drive both attacks and mitigations.  
- 🐘 Implemented a fully **insecure PHP backend** that cleanly reproduces all **OWASP Top 10** categories.  
- 💣 Executed successful exploits (**SQLi, XSS, XXE, IDOR, insecure upload, etc.**) and documented results.  
- 🛡️ Engineered a **secure PHP backend**, applying correct, specific fixes for each vulnerability.  
- ⚡ Developed a hardened **Laravel API** with framework best practices  
  *(validation, policies, CSRF, throttling, env secrets, structured logging)*.  
- 📹 Produced **demo assets** (attack vs fix) and wrote **documentation** to help others reproduce & learn.  
- 📂 Managed a repository of **179 files**, coordinating **1 Frontend + 3 Backends** with consistent UX across layers.  


---

## 🔭 What This Project Proves

This repository is a **complete, teachable journey** from insecure to secure systems:

- Built a **polished Svelte frontend** to interact with realistic flows (auth, booking, uploads, admin).
- Coded an **intentionally insecure PHP backend** to **reliably reproduce** OWASP Top 10 issues.
- **Exploited** all 10 categories with tooling (Burp Suite) and **recorded successful attacks**.
- Implemented a **secure PHP backend** with concrete fixes (prepared statements, output encoding, RBAC, CSRF, etc.).
- Delivered a **hardened Laravel API** with framework-grade protections and best practices.
- Created **side-by-side comparisons**: vulnerable vs fixed behavior.
- Produced **documentation** (this `README.md`) and prepared **slides/notes/video clips** for demos.

You’re not just seeing *what* was done—you can **re-run the insecure app**, **replicate the hacks**, then **validate the mitigations**.

