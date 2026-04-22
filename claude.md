# Guidance Management System (GMS) for CHMSU - Full Functionality

## 1. User Roles & Access Control (RBAC)
To ensure data security, the system will use Role-Based Access Control. I recommend integrating this with CHMSU’s existing Google Workspace or Microsoft 365 for Single Sign-On (SSO) using their `@chmsu.edu.ph` emails.

* **Super Admin (System Administrator):** Manages system settings, user accounts, role assignments, and system backups.
* **Guidance Director / Head:** Full access to all counselor schedules, aggregate analytics, reports, and department oversight.
* **Guidance Counselors:** Access to assigned students' profiles, scheduling, confidential case notes, testing records, and referrals.
* **Students:** Can view their own non-confidential profile, book appointments, request certificates/clearances, and take online assessments.
* **Faculty/Staff:** Limited access purely for submitting Student Referral Forms and tracking the status of their referrals.

## 2. Core Modules & Functionalities

### A. Student Profiling & Cumulative Record Module
This is the digital equivalent of the student's "brown envelope" in the guidance office.
* **Digital Intake Forms:** Freshmen/transferees can fill out their initial cumulative data online (family background, educational history, socioeconomic status, emergency contacts).
* **Academic Tracking:** Integration with the main CHMSU Student Information System (SIS) to pull course, year level, and academic standing (to identify at-risk students).
* **Document Vault:** Secure, encrypted uploads for physical documents (birth certificates, medical records, signed consent forms).

### B. Appointment & Counseling Management Module
* **Self-Service Booking:** Students can view a counselor's available time slots and book sessions (Academic, Personal/Social, or Career Counseling) via a calendar interface.
* **Automated Notifications:** Email and SMS reminders (using an API like Twilio or Semaphore) for upcoming appointments to reduce no-shows.
* **Confidential Case Notes:** A highly encrypted text editor for counselors to log session notes. **Crucial:** These notes should require a secondary PIN or biometric prompt from the counselor to decrypt and view, ensuring even database admins cannot read them in plain text.
* **Tele-counseling Integration:** Auto-generation of Google Meet or Zoom links for virtual sessions.

### C. Referral & Anecdotal Module
* **Faculty Referral System:** A standardized form for instructors to flag students exhibiting concerning behavior, excessive absences, or failing grades.
* **Intervention Tracking:** Counselors can update the status of the referral (e.g., "Student Contacted," "Counseling Ongoing") so the referring faculty knows action was taken without exposing confidential details.
* **Disciplinary Integration:** A sub-module to track minor and major offenses, linked to the Prefect of Discipline if required by CHMSU's structure.

### D. Psychological Testing & Evaluation Module
* **Test Inventory Management:** Track physical and digital test materials (e.g., IQ tests, personality tests, career aptitude tests).
* **Scheduling & Roster:** Schedule batch testing for specific colleges or year levels.
* **Results Logging & Interpretation:** A secure area to input test percentiles and upload the counselor's interpretation reports.
* **Automated Career Matching:** For career aptitude tests, the system can generate a list of suggested career paths based on the student's encoded results.

### E. Clearance & Certification Module
* **Good Moral Certificate Generation:** Automated generation of Good Moral Character certificates in PDF format with dynamic data insertion (Student Name, Program, Date) and digital signatures.
* **Exit Interviews:** Seniors requesting graduation clearance must first complete an automated online exit survey before their clearance is marked "Approved" in the system.

### F. Analytics & Reporting Dashboard
* **Demographic Reports:** Real-time charts showing student populations by college, gender, or risk level.
* **Counseling Metrics:** Track the most common reasons for counseling (e.g., anxiety, academic stress, financial issues) to help CHMSU implement targeted mental health programs.
* **Exportable Reports:** One-click generation of monthly or annual accomplishment reports for CHED (Commission on Higher Education) or university administration in Excel/PDF formats.

## 3. Technical Stack Recommendations (Updated for XAMPP)

To build this efficiently and ensure full compatibility with local XAMPP environments, I recommend a robust PHP stack:

* **Local Environment:** XAMPP (Apache web server).
* **Backend:** PHP with the **Laravel** framework. Laravel is excellent for university systems because it has built-in routing, authentication, and security features out-of-the-box.
* **Database:** MySQL / MariaDB (included in XAMPP). Laravel's Eloquent ORM makes interacting with MySQL incredibly smooth and protects against SQL injection.
* **Frontend:** Laravel Blade templates styled with TailwindCSS or Bootstrap. Alternatively, you can use Vue.js or React natively integrated into Laravel via Inertia.js.
* **Security:** Laravel's built-in CSRF protection, AES-256 encryption using Laravel's `Crypt` facade for the confidential case notes, and session-based authentication (like Laravel Breeze or Jetstream).