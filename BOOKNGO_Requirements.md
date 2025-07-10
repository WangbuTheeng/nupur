
# 📘 Bus Booking System for Nepal – Requirements Document

## 📌 Project Title  
**BOOKNGO – Festival-Optimized Bus Ticketing System**

---

## 🧩 Objective  
To develop a Laravel-based online bus ticket booking system tailored for Nepal, optimized for high-demand travel seasons such as Dashain, Tihar, and Chhath. It provides a user-friendly interface for passengers, smart algorithms, and robust management tools for bus operators and booking agents.

---

## ✅ Functional Requirements

### 1. User Module (Passengers)
- [ ] User registration and login (email/phone-based)
- [ ] View and search available buses by:
  - Source and destination
  - Travel date
  - Festival tag (e.g., "Dashain Routes")
- [ ] Real-time seat layout view and selection
- [ ] Online payments via:
  - eSewa
  - Khalti
  - Fonepay
  - ConnectIPS
- [ ] Group/family booking support
- [ ] View booking history, cancel bookings
- [ ] Festival pre-booking alerts (via SMS/email)
- [ ] Apply discount or festival coupons
- [ ] Download/view digital tickets (with QR)
- [ ] Join waiting list for full buses
- [ ] Auto-book seat if one becomes available (smart cancellation)

### 2. Admin Module (Bus Operators)
- [ ] Admin login/authentication
- [ ] Add/update/delete:
  - Buses and their types
  - Routes, stops, and schedules
  - Fare adjustments for peak seasons
- [ ] Create festival-specific bus schedules
- [ ] View all bookings, revenue reports
- [ ] Block/reserve seats (e.g., VIP or emergency)
- [ ] Assign ticket agents for offline booking
- [ ] Manage complaints and feedback
- [ ] View travel heatmaps and analytics during festivals
- [ ] Export booking reports to Excel/PDF

### 3. Agent Module (Offline Booking Sync)
- [ ] Agent login (assigned by admin)
- [ ] Manual booking for walk-in/offline users
- [ ] View available buses and seats
- [ ] Sync offline bookings with online system
- [ ] Print physical ticket receipts

---

## 🤖 Non-Functional Requirements
- [ ] Mobile-responsive design
- [ ] Multilingual support (English + Nepali)
- [ ] Fast API response time (< 1s)
- [ ] Secure user authentication (JWT)
- [ ] SMS/Email notification service
- [ ] Payment gateway compliance with Nepali standards
- [ ] Scalable deployment (supports 1000+ concurrent users)
- [ ] Auto-cancel seat hold if not paid within X minutes
- [ ] Role-based access control (admin, agent, user)

---

## 🔁 Smart Algorithms

### 1. Seat Allocation Algorithm
- First-come-first-serve with seat lock
- Group booking seat clustering
- Festival-based seat reservation quota (e.g., 30% pre-reserved)
- Auto-cancel unpaid reservations after timeout

### 2. Bus Recommendation Algorithm
- Uses:
  - User’s past booking routes
  - Peak time availability
  - Travel preferences (bus type, time)
- Outputs top 3 bus suggestions

### 3. Waiting List & Smart Cancellation Logic
- User joins waitlist for full buses
- If a seat is canceled or not confirmed in time:
  - Automatically assigns seat to first waitlisted user
  - Sends SMS/Email confirmation

### 4. Travel Heatmap & Festival Traffic Prediction
- Uses:
  - Booking volume by route/date
  - Historical data from past festivals
- Outputs:
  - Real-time heatmap of high-demand routes
  - Alerts to users about likely-to-fill routes
  - Operator insight into peak load times

---

## 🛠️ Technology Stack

| Layer         | Technology                          |
|---------------|-------------------------------------|
| Backend       | Laravel 11 (JWT Auth, REST APIs)    |
| Database      | MySQL                  |
| Frontend      | Blade + TailwindCSS        |
| Payment API   | eSewa, Khalti, Fonepay, ConnectIPS  |
| Maps/Routes   | Google Maps API                     |
| Deployment    | DigitalOcean / Railway.app          |
| Notification  | Nexmo/Sparrow SMS, Email (SMTP)     |

---

## 🔄 Development Methodology

- **Agile Development**
  - Weekly sprints
  - Iterative testing and user feedback
  - Flexible backlog updates based on demand
  - Continuous deployment with CI/CD pipelines

---

## 📅 Festival Features

- Pre-booking window for Dashain, Tihar, etc.
- Popular route highlighting
- Dynamic pricing during festival seasons
- Alerts for early booking and discount periods
- Special route sections (e.g., “Top Dashain Buses”)
- Agent support in rural/non-digital regions

---

## 📊 Admin Analytics & Reporting

- Daily/weekly/monthly bookings chart
- Revenue trends by route/operator
- Festival traffic heatmap and surge analysis
- Ticket status breakdown (confirmed, canceled, waitlisted)
- Export reports (PDF, Excel)

---

## 📦 Additional Features (Future Scope)

- In-app notifications for delays, reminders
- Referral/affiliate system for growth
- Mobile app (Flutter or React Native)
- Live bus tracking using GPS API
- Driver module with real-time trip updates

---

## 🎯 Conclusion

BOOKNGO aims to digitize and revolutionize Nepal’s bus ticketing experience, especially during chaotic festival travel seasons. By combining smart algorithms, agent-based support, dynamic analytics, and deep local integration (payment, language, offline sync), this system provides unmatched convenience and reliability for both users and operators.
