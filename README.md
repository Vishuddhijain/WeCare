# WeCare - Doctor Appointment Booking Platform 💖

WeCare is a simple yet functional **Doctor Appointment Booking Web Application** built using PHP and MySQL. It allows both **patients** and **doctors** to manage healthcare appointments easily.

---

## 🌟 Features

### 👤 For Patients

* 👉 **Secure Login/Signup** required before making an appointment
* 👉 **Book an appointment** with available doctors & time slots
* 👉 **Upload previous lab reports** (PDF only) while booking
* 👉 **View your upcoming and past appointments**
* 👉 **Delete an appointment** if needed
* 👉 **Give review & rating** to the doctor after the appointment
* 👉 **Download payment receipt** for any appointment

---

### 🎠 For Doctors

* 👉 **Secure Login/Signup for doctors**
* 👉 **View doctor profile**
* 👉 **See reviews and ratings** given by patients
* 👉 **View appointments with patients**

---

## 🚀 User Flow

1. User visits the site (`index.php`)
2. If user clicks on **Appointment** or **Book Appointment** → must login first
3. After login, patient can select doctor, time slot, upload report, and book appointment
4. Patient can view their appointments, delete, or download receipt
5. Doctors can login, view their appointments, see reviews from patients

---

## 🛠️ Tech Stack

* **PHP** (server-side scripting)
* **MySQL** (database)
* **HTML, CSS, JS** (frontend)
* **AJAX (Fetch API)** for dynamic loading of doctor slots

---

## ⚙️ Setup Instructions

1. Clone this repo:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY_NAME.git
```

2. Import the database (provided in the SQL file) into your MySQL server.

3. Configure your database connection in the project:

```php
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');
```

4. Run the project on your local server (XAMPP / WAMP / MAMP):

```bash
http://localhost/your_project_folder/index.php
```

