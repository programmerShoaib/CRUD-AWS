# CRUD-AWS
aws crud



To implement the DevOps Task Tracker from a completely fresh EC2 instance, follow this total system flow. This covers everything from the initial OS updates to the final glassmorphism UI.

Step 1: System & Dependency Installation
Run these commands to prepare your Amazon Linux 2023 environment. This installs the "Engine" (Apache/PHP) and the "Data Store" (MariaDB).

Bash

# 1. Update system packages
sudo dnf update -y

# 2. Install Apache, PHP 8.2 (with MySQL support), and MariaDB
sudo dnf install -y httpd mariadb105-server php php-mysqli php-fpm php-json

# 3. Start and enable all services
sudo systemctl enable --now httpd
sudo systemctl enable --now mariadb
sudo systemctl enable --now php-fpm

# 4. Set directory permissions for the web user
sudo usermod -a -G apache ec2-user
sudo chown -R ec2-user:apache /var/www
sudo chmod 2775 /var/www && find /var/www -type d -exec sudo chmod 2775 {} \;
Step 2: Database Architecture Setup
Now, create the database and the specific table for the Task Tracker.

Enter MariaDB: sudo mysql -u root

Execute the Schema:

SQL

CREATE DATABASE taskforce_db;
CREATE USER 'dev_user'@'localhost' IDENTIFIED BY 'DevPass123!';
GRANT ALL PRIVILEGES ON taskforce_db.* TO 'dev_user'@'localhost';
FLUSH PRIVILEGES;

USE taskforce_db;
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    priority ENUM('Low', 'Medium', 'High') NOT NULL,
    due_date DATE NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
EXIT;

Step 3: Deployment of the "Glassmorphism" Logic
Create the main application file. This code handles the Dark Mode UI and the CRUD operations.

Create file: nano /var/www/html/tasks.php

Paste this full code:     

     your code index.php


Step 4: Final Flow Verification
Network: Go to AWS Dashboard > EC2 > Security Groups. Ensure Port 80 is open.

Access: Visit http://YOUR_IP/tasks.php.

Operation: Create a task with "High" priority. The UI should instantly show a red-tinted badge.