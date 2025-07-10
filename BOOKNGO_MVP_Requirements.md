
# 📘 BOOKNGO – Simplified Bus Booking System for Nepal (MVP)

## 📌 Project Title  
**BOOKNGO – Festival-Optimized Bus Ticketing System (MVP Version)**

---

## 🧩 Objective  
To develop a simple, Laravel-based bus ticket booking system for Nepal with a clean UI and essential features. Focused on ease of use and rapid deployment, the MVP will support core functionalities such as online booking, seat selection, and admin control, with festival support and advanced features to be added later.

---

## ✅ MVP Functional Requirements

### 1. User Module (Passengers)
- [x] User registration and login (email/phone-based)
- [x] View and search available buses by:
  - Source and destination
  - Travel date
- [x] Real-time seat layout view and selection
- [ ] Online payment via:
  - Khalti (initial integration)
- [x] View booking history
- [ ] Download or view digital tickets (with QR)

### 2. Admin Module (Bus Operators)
- [x] Admin login/authentication
- [x] Add/update/delete:
  - [x] Buses and their types
  - [x] Routes, stops, and schedules
  - [x] Admin interface (functional)
- [x] Reserve or block seats (seat management ready)
- [x] View all bookings
- [x] View total revenue (basic summary)

---

## ❌ Postponed Features (To Add After MVP)
- Festival-specific pre-booking and alerts
- Dynamic pricing and festival tagging
- Bus recommendation algorithm
- Agent-based offline booking
- Waiting list and smart auto-booking
- Multilingual support (Nepali)
- Multiple payment gateways (eSewa, Fonepay, ConnectIPS)
- Travel heatmaps and analytics dashboard
- PDF/Excel exports
- In-app notifications

---

## 🤖 MVP Non-Functional Requirements
- [x] Mobile-responsive design
- [/] Secure user authentication (session-based, JWT pending)
- [x] Simple UI with Blade + TailwindCSS
- [x] Scalable Laravel 11 backend with MySQL

---

## 🛠️ Technology Stack

| Layer         | Technology                      |
|---------------|---------------------------------|
| Backend       | Laravel 11 (JWT Auth)           |
| Database      | MySQL                           |
| Frontend      | Blade + TailwindCSS             |
| Payment API   | Khalti (initial integration)    |
| Deployment    | DigitalOcean or Railway.app     |

---

## 🔄 Development Methodology

- **Agile Approach**
  - Weekly sprints
  - Build and test core functionality first
  - Gradually introduce complex features

---

## 🎯 Conclusion

This MVP version of BOOKNGO is focused on delivering a minimal yet functional ticket booking system tailored for the Nepalese market. It will support real-world users during normal and festival times with a smooth UX, efficient admin controls, and future-ready architecture.

