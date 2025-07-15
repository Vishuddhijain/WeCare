# 🩺 WeCare – Doctor Appointment Booking Platform

**WeCare** is a simple yet functional **Doctor Appointment Booking Web Application** built using **PHP and MySQL**. It allows both **patients** and **doctors** to manage healthcare appointments with ease — from booking and uploading reports to reviewing doctors and downloading payment receipts.

---

## 🚀 Features

### 👤 For Patients
- ✅ **Secure Login/Signup** required before making an appointment  
- ✅ **Book an appointment** with available doctors and time slots  
- ✅ **Upload previous lab reports** (PDF only) while booking  
- ✅ **View upcoming and past appointments**  
- ✅ **Cancel/delete an appointment** if needed  
- ✅ **Submit reviews and ratings** for doctors after appointments  
- ✅ **Download payment receipts** for appointments  

### 🩺 For Doctors
- ✅ **Secure Login/Signup** for doctors  
- ✅ **View personal profile**  
- ✅ **View reviews and ratings** received from patients  
- ✅ **See all scheduled appointments**  

---

##  User Flow

1. User visits the site [`index.php`](index.php)  
2. If the user clicks on **"Appointment"** or **"Book Appointment"**, they are prompted to log in first  
3.  After login:
   - **Patients** can:
     - Select a doctor  
     - Choose a time slot  
     - Upload reports  
     - Confirm and manage bookings  
   - **Doctors** can:
     - View their dashboard  
     - Check patient reviews  
     - See scheduled appointments  

---

## 🛠 Tech Stack

| Technology       | Purpose                            |
|------------------|------------------------------------|
| **PHP**          | Backend server-side scripting      |
| **MySQL**        | Relational database management     |
| **HTML/CSS**     | Frontend structure and styling     |
| **JavaScript**   | Client-side interactivity          |
| **AJAX (Fetch)** | Dynamic doctor-slot loading        |

---

##  Setup Instructions

### Clone the Repository
```
git clone https://github.com/Vishuddhijain/WeCare.git
```
** Import the Database**
1. Open phpMyAdmin
2. Create a new database named: contact_db
3.Import the contact_form.sql file located inside the project folder

** Configure Database Connection**
Ensure the following line exists in your PHP files (e.g., index.php, appointment.php, etc.):
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

** Run the Project Locally**
1. Start XAMPP/WAMP/MAMP
2. Move the WeCare folder into your local server directory (e.g., htdocs)
3. Open your browser and navigate to:
http://localhost/WeCare/index.php


