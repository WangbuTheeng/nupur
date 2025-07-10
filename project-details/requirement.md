🚌 BookNGO – Complete Bus Booking System Prompt
Project Summary:
Develop a comprehensive Laravel-based bus booking system called BookNGO, designed for the Nepali market with special features for festivals like Dashain, Tihar, and Chhath. The system must include three user roles: Admin, Bus Operator, and Customer. Each user role has distinct access, responsibilities, and dashboards.

👤 1. Admin Module
Role:
Super administrator for the whole platform

Functionalities:
Operator Management

Create, update, and delete Bus Operator accounts

Assign credentials and permissions

View operator activity and performance

User Management

Manage end users/customers (view, block/unblock, delete)

View customer booking history

System-Wide Bus & Route Control

View and manage all bus details across all operators

Add/edit/delete global routes, cities, bus types

Trip & Schedule Oversight

Monitor all trip schedules

View daily and monthly scheduled buses

Filter schedules by operator, route, or date

Booking & Revenue Monitoring

See all bookings from all users/operators

View:

Today's Bookings

Monthly Bookings

Today's Revenue

Monthly Revenue

Counter Booking Supervision

Track bookings made manually by operators at their physical counters

Reports & Analytics

Generate reports by operator, route, time period

Export data (PDF/Excel)

Festival Booking Control (Optional)

Enable/disable special high-demand schedules during Dashain, Tihar, Chhath

🏢 2. Operator (Bus Company) Module
Role:
Bus company account with their own bus listings and trips

Functionalities:
Bus Management

Add/edit/delete buses

Set capacity, features (AC/Non-AC), bus type (Deluxe, Super Deluxe)

Define seating layout (2x2, 3x2, etc.)

Schedule & Route Management

Create and manage trip schedules

Assign buses to routes and time slots

Set fares and departure times

Booking Management

View all bookings for their buses

Approve or cancel customer bookings

Check seat availability in real time

Counter Booking Feature

Manually book seats for customers who come to physical counters

Print tickets or provide booking IDs

Revenue Dashboard

See today's and monthly revenue

Track earnings per route or trip

Real-Time Seat Status

Block/unblock specific seats

See filled vs available seats per trip

👥 3. Customer (User) Module
Role:
Regular users booking tickets online

Functionalities:
User Registration & Login

Register/login with email or phone

OTP/email verification for account creation

Bus Search

Search available buses by:

From & To Locations

Date

Operator

Bus type

Seat Selection & Booking

Choose available seat visually on seat layout

Book multiple seats

Add passenger details (name, age, gender)

Online Payment

Integrated payment gateways (eSewa, Khalti, FonePay, IME Pay)

Booking only confirmed after payment success

E-Ticket Generation

View and download PDF ticket after booking

Ticket should have booking ID, seat no, bus info, departure time

Booking History

View past and upcoming bookings

Cancel upcoming bookings if allowed by operator policy

Print Ticket Option

Generate and print ticket from browser or mobile

🔁 Booking Flow Summary
Customer searches for buses → selects operator and seats → makes payment

System blocks the seat during checkout → confirms after payment

Ticket is generated, available in dashboard

Operator sees the booking in real-time

Admin can monitor everything

🔐 Role-Based Access Control (RBAC)
Feature / Module	Admin	Operator	Customer
Manage Operators	✅	❌	❌
Manage Buses	✅	✅	❌
Create Trips/Schedules	✅	✅	❌
Book Seats	❌	✅ (Counter)	✅
View Bookings	✅	✅	✅ (own)
Manage Payments	✅	✅ (own)	✅
Download Tickets	❌	✅	✅
Reports & Analytics	✅	✅	❌

💻 Technical Notes
Tech Stack: Laravel (Backend), Blade/Vue.js (Frontend), MySQL

Payment Gateways: eSewa, Khalti, FonePay (test/live environment)

Seat Layout: Must support dynamic rows/columns per bus

PDF Ticket Generation: Use DomPDF or Snappy PDF

Festival Mode: Enable custom schedules with price hike for specific dates

