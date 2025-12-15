Quiz Management System (PHP & MySQL)

A complete Quiz Management System built using PHP, MySQL, HTML, CSS, and JavaScript.
The system supports Admin and User roles, allows dynamic creation of Quizzes → Rounds → Questions, and tracks user performance securely.

Features
      Admin
            Secure admin login
            Create & manage quizzes
            Add multiple rounds per quiz
            Add questions round-wise
            Manage users
            View quiz results & user performance
      User
            Register & login securely
            View available quizzes
            Select quiz → round → attempt quiz
            Timer-based questions
            View personal results and total score
      Security
            Password hashing (password_hash)
            Session-based authentication
            Role-based access control
            SQL injection protection using prepared statements


Tech Stack
      Frontend: HTML, CSS (Flexbox), JavaScript
      Backend: PHP
      Database: MySQL
      Server: Apache (XAMPP)


Project Structure 

quiz-system/
│
├── admin/
│   ├── dashboard.php
│   ├── manage-quizzes.php
│   ├── manage-rounds.php
│   ├── add-question.php
│   ├── manage-questions.php
│   ├── manage-users.php
│   ├── view-results.php
│   └── view-user-performance.php
│
├── user/
│   ├── dashboard.php
│   ├── quiz-rounds.php
│   ├── my-results.php
│
├── question/
│   └── round.php
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── quiz/
│   └── save-results.php
│
├── css/
│   ├── auth.css
│   ├── admin.css
│   ├── user.css
│   └── question.css
│
├── js/
│   └── script.js
│
├── .gitignore
├── README.md
└── index.html



Database Schema
      users
            user_id (PK)
            username (unique)  
            email
            password (hashed)
            role (ADMIN / USER)
      quizzes
            quiz_id (PK)
            title
            is_active
            created_at
      rounds
            round_id (PK)
            quiz_id (FK)
            round_title
            time_limit
      questions
            question_id (PK)
            round_id (FK)
            question_text
            option_a
            option_b
            option_c
            option_d
            correct_option
            explanation
      results
            result_id (PK)
            user_id (FK)
            quiz_id (FK)
            round_id (FK)
            total_score
            correct_answers
            wrong_answers
            submitted_at
