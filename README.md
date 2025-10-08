# 🖼️ Gallery of Wonders
**Preserving and showcasing creative works & performances**

A dynamic web platform where users can upload, showcase, and manage their creative works — including art, photography, and writing.  
Built using **HTML, CSS, JavaScript, PHP, and MySQL**.

---

## 📜 Project Description
**Gallery of Wonders** is a web-based archive for creative expression.  
Users can sign up, upload their works, and explore others’ creations.  
Each user has a personal dashboard to organize, update, and manage their uploaded content.

---

## ⚙️ Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript  
- **Backend:** PHP (Procedural or PDO)  
- **Database:** MySQL  
- **Server Environment:** XAMPP / WAMP / LAMP  
- **Optional Libraries:** Bootstrap (for responsive UI)

---

## 📂 Directory Structure
```
gallery_of_wonders/
│
├── index.php               # Homepage
├── login.php               # User login page
├── logout.php              # logout page
├── register.php            # User registration page
├── upload.php              # Upload creative works
├── dashboard.php           # User dashboard
├── view_work.php           # View single work details
│
├── includes/
│   ├── db_connect.php      # Database connection
│   ├── header.php          # Common header
│   ├── footer.php          # Common footer
│
├── assets/
│   ├── css/
│   │   └── style.css       # Main stylesheet
│   ├── js/
│   │   └── script.js       # Frontend scripts
│   ├── uploads/             # Uploaded artworks              
│
└── sql/
    └── gallery_db.sql      # MySQL database script
```

---

## 🧩 Features

### 🔹 Basic Features
- User Registration & Login System  
- Upload and showcase creative works (art, photography, writing, etc.)  
- Organize content into boards/collections  
- Like/Save/Bookmark other works  
- Simple search & filter by category  
- Personal dashboard to manage uploaded works  


---

## 🧰 Installation & Setup
1. **Clone or download** this project folder:
   ```bash
   git clone https://github.com/piyushgautamgr8/Gallery_of_Wonders
   ```
2. **Move** the folder into your web server directory:
   - For XAMPP → `C:\xampp\htdocs\gallery_of_wonders`
   - For WAMP → `C:\wamp\www\gallery_of_wonders`
3. **Import the database:**
   - Open **phpMyAdmin**
   - Create a database named `gallery_db`
   - Import the file `/database/gallery_db.sql`
4. **Configure database connection:**
   - Open `/includes/db_connect.php`  
     Update your credentials:
     ```php
     $servername = "127.0.0.1";
     $username = "root";
     $password = "";
     $dbname = "gallery_db";
     ```
5. **Run the app:**  
   Open your browser and visit:
   ```
   http://localhost/gallery_of_wonders/
   ```

---

## 🔒 Security Notes
- Passwords are hashed using PHP `password_hash()`  
- All SQL queries use **prepared statements** to prevent SQL injection  
- File uploads restricted to specific formats (e.g., JPG, PNG, PDF)  

---

## 🚀 Future Improvements
- Integrate AI models for tagging & recommendations  
- Add API endpoints for external access  
- Improve UI with dark/light mode toggle  
- Enable multi-language support  

---

## 👨‍💻 Developer
**Project:** Gallery of Wonders  
**Developed using:** HTML, CSS, JavaScript, PHP, MySQL  
**Author:** *Piyush Gautam,     Mohd Sufiyan,     Vadithya Prashanth
