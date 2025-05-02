# 🎓 Attendance Scheduling Backend

This is the backend API for the **Smart Attendance & Scheduling System**, a robust Laravel-based project designed to manage academic class scheduling, attendance tracking, and role-specific access for administrators, instructors, and students.

---

## 🚀 Features

* 🔐 **JWT-based Authentication** using `tymon/jwt-auth`
* 👨‍🏫 Role-based API access for:

    * Admins
    * Instructors
    * Students
* 🧑‍🎓 Batch & Class Management

    * Create, update, and view batches
    * Assign instructors and students to batches
    * Schedule and manage classes
* 📊 Attendance Recording & Statistics

    * Mark attendance by instructors
    * View attendance summaries
* 📅 Upcoming Classes

    * For both instructors and students
* 📁 Clean API structure with standardized success/error responses
* 🔄 Scheduler Ready (for future cron tasks)
* 🌐 [Postman API Collection](https://www.postman.com/arabin-6497/attendance-scheduling/overview)

---

## 🛠️ Technologies Used

* **PHP 8.2**
* **Laravel 12**
* **JWT Authentication** via `tymon/jwt-auth`
* **Sanctum** for token security
* **FakerPHP** for realistic database seeding

---

## 📥 Getting Started

### 🧾 Prerequisites

Ensure your machine has the following:

* PHP ≥ 8.2
* MySQL or XAMPP running
* Composer

### 📦 Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/azn-arabin/attendance-scheduling-backend.git
   ```

2. **Navigate into the project folder:**

   ```bash
   cd attendance-scheduling-backend
   ```

3. **Copy `.env` file and configure:**

   ```bash
   cp .env.example .env
   ```

    * Update the `.env` file with your database credentials and other environment settings.

4. **Install dependencies:**

   ```bash
   composer install
   ```

5. **Generate app key:**

   ```bash
   php artisan key:generate
   ```

6. **Run migrations:**

   ```bash
   php artisan migrate
   ```

7. **Generate JWT secret key:**

   ```bash
   php artisan jwt:secret
   ```

8. **(Optional) Seed database with sample data:**

   ```bash
   php artisan db:seed
   ```

9. **(Optional) Run scheduled tasks manually:**

   ```bash
   php artisan schedule:work
   ```

10. **Start the development server:**

    ```bash
    php artisan serve
    ```

---

## 🌐 API Base URL

All API endpoints are prefixed with:

```
http://localhost:8000/api
```

Use the Postman collection to explore available endpoints.

📎 [Postman Collection](https://www.postman.com/arabin-6497/attendance-scheduling/overview)

