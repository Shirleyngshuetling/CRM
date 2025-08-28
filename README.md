# CRM

A lightweight **Customer Relationship Management (CRM)** web application built using **PHP**, **JavaScript**, and **CSS**.  
This project supports core CRM functionalities such as managing customers, leads, interactions, reminders, and users.  
It’s designed for small teams seeking a straightforward, self-hosted CRM solution.

---

## 📂 Project Structure

- **PHP**: Core backend logic for handling CRM entities.
  - `index.php` – Main dashboard entry point  
  - `dashboard_main.php`, `customer_list.php`, `lead_list.php` – Interfaces for CRM modules
- **assets/** – Static resources
  - `css/` – Stylesheets  
  - `js/` – JavaScript for interactivity
- **Entity Handlers**:
  - `add_customer.php`, `add_lead.php`, `add_interaction.php`, `add_user.php`, `add_reminder.php`
  - `update_customer_lead.php`, `update_interaction.php`, `update_user.php`

---

## 🚀 Features

- **Customer Management** – Add, update, and list customer details  
- **Lead Management** – Track and manage leads  
- **Interaction Tracking** – Log client interactions and activities  
- **Reminders & Scheduling** – Add reminders to follow up with clients  
- **User Administration** – Manage users and roles  

---

## 🛠️ Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/Shirleyngshuetling/CRM.git
```

### 2. Set Up Development Environment
- Install **XAMPP**, **MAMP**, or any PHP environment  
- Place project files inside your web server directory (e.g., `htdocs` for XAMPP)

### 3. Database Setup
- Import your database schema into **MySQL**  
- Update DB credentials inside relevant PHP files  

### 4. Run the Application
Open your browser and go to:
```
http://localhost/CRM/index.php
```

---

## 💻 Tech Stack

| Layer    | Technology          |
| -------- | ------------------- |
| Backend  | PHP                 |
| Frontend | JavaScript          |
| Styles   | CSS                 |
| Database | MySQL (recommended) |

---

## 🤝 Contributing

Contributions are welcome! You can help by:

- Fixing bugs or enhancing features
- Improving UI/UX with responsive design
- Refactoring backend code
- Adding authentication and security features

**Steps to Contribute:**
1. Fork the repo  
2. Create a new branch:
   ```bash
   git checkout -b feature/[feature-name]
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add [feature]"
   ```
4. Push your branch:
   ```bash
   git push origin feature/[feature-name]
   ```
5. Open a Pull Request

---

## 📜 License

This project is currently **unlicensed**. Please contact the author for usage rights.

---

## 📧 Contact

If you have questions, suggestions, or need setup help:

- Open an **issue** on GitHub
- Or reach out via this repository
