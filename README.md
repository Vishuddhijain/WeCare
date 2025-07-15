# 🩺 WeCare – Doctor Appointment Booking Platform

**WeCare** is a simple yet functional **Doctor Appointment Booking Web Application** built using **PHP and MySQL**. It allows both **patients** and **doctors** to manage healthcare appointments with ease — from booking and uploading reports to reviewing doctors and downloading payment receipts.

**Fully responsive and mobile-friendly** — the UI adapts to different screen sizes for better usability on smartphones, tablets, and desktops.

---

## Features

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

## User Flow

1. User visits the site [`index.php`](index.php)  
2. If the user clicks on **"Appointment"** or **"Book Appointment"**, they are prompted to log in first  
3. After login:
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

## Tech Stack

| Technology       | Purpose                            |
|------------------|------------------------------------|
| **PHP**          | Backend server-side scripting      |
| **MySQL**        | Relational database management     |
| **HTML/CSS**     | Frontend structure and styling     |
| **JavaScript**   | Client-side interactivity          |

---

##  Setup Instructions

### Clone the Repository

```bash
git clone https://github.com/Vishuddhijain/WeCare.git

**Import the Database**
Open phpMyAdmin

Create a new database named: contact_db

Import the contact_form.sql file located inside the project folder

Configure Database Connection
In your PHP files (e.g., index.php, appointment.php, etc.), ensure this line exists:

php
Copy code
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');
Run the Project Locally
Start XAMPP/WAMP/MAMP

Move the WeCare folder into your local server directory (e.g., htdocs)

Open your browser and navigate to:
Copy code
http://localhost/WeCare/index.php



yaml
Copy code

---

Let me know if you’d like help creating responsive screenshots, adding live demo link
