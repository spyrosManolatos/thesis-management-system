# 🎓 Thesis Management System

> CEID - University of Patras | Web Programming Lab Project (2025)

This project is a full-stack **Thesis Management System** developed as part of the *"Web Programming and Systems on the World Wide Web"* lab course at the Computer Engineering & Informatics Department (CEID), University of Patras.

The application allows students, professors, and the secretariat to manage all aspects of thesis assignments — from topic submission to final grading — through a unified, role-based web platform.

---

## 🚀 Features

### 🧑‍🎓 Student
- Send invitations to professors to form a committee
- Submit draft thesis and final links (e.g., Nemertes)
- Schedule live presentations
- Edit personal profile

### 👨‍🏫 Professor
- Create and manage thesis topics
- Assign topics to students
- Submit notes and grades
- Download thesis data in JSON/CSV format
- View statistics (average grade, duration, etc.)

### 🗃️ Secretariat
- Manage official topic approvals
- Finalize thesis completions
- Monitor ongoing, under-review, and graded theses

---

## 🛠️ Technologies Used

- **PHP** – Backend logic and API endpoints
- **JavaScript (Vanilla)** – Dynamic content and AJAX with `fetch()`
- **HTML5 / CSS3** – Page structure and styling
- **Bootstrap** – Responsive design and layout
- **Chart.js** – Visual display of thesis statistics
- **Quill.js** – Rich-text editing for notes
- **MySQL** – Relational database
- **XAMPP (Apache + MySQL)** – Local server hosting

---

## 📁 Project Structure

```
/assets         → CSS, JS, and image resources  
/views          → Role-based views (student, professor, secretary)  
/includes       → Authentication, DB configs, and shared utilities  
/assignments    → Thesis assignment APIs  
/examination    → Grading and review APIs  
/student        → Student-specific APIs  
/secretary      → Secretariat APIs  
```

---

## 🧠 Architecture

The system follows a **3-tier architecture**:

- **Frontend**: HTML/CSS/JS with Bootstrap
- **Backend**: PHP endpoints interacting with the database
- **Database**: MySQL, queried via PHP using PDO

Authentication and authorization are handled using `$_SESSION` variables, with dynamic content loaded via AJAX.

---

## 🧪 Testing

- Optimized for minimal server load via client-side caching
- Custom 404 page handling via `.htaccess`
- Tested for response times with and without browser cache
- AJAX-based interface updates to avoid full page reloads

---

```
📌 Home Page  
🔐 Login Page  
📚 Professor Dashboard  
📈 Thesis Stats Chart  
```

---

## 📝 License

This project is **not open source**. All rights reserved.

You may not use, copy, modify, or distribute this code without explicit written permission from the authors.

For licensing inquiries, contact:
- up1100496@ac.upatras.gr
- up1104802@ac.upatras.gr
- up1100617@ac.upatras.gr

---

## 🤝 Contributors

- **Paraskevas Vallianatos** (up1100496@ac.upatras.gr)  
- **Spyros Manolatos** (up1104802@ac.upatras.gr)  
- **Andreas-Erikos Maroudas** (up1100617@ac.upatras.gr)
