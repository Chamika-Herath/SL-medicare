# Laravel Cloud-Based Hospital Management System (HMS)

This repository contains the source code, virtualization configurations, database migrations, and performance testing tools for a secure, cloud-native Hospital Management System (HMS) built using **Laravel** (PHP framework) and **Blade Templates**.

It features a multi-tenant client portal (for Patients, Doctors, and Admins) with role-based dashboard metrics, appointment management, and direct integration with cloud object storage for diagnostic scans.

---

## 📂 Repository Structure

- `app/`: Laravel core models, controllers, and custom middleware for role checks.
- `routes/`: Routing files (`web.php` for portal, `api.php` for REST endpoints).
- `resources/views/`: Blade UI views (portal layouts, dashboard panels, scheduler, records).
- `database/`: Database migration structures and data seeders.
- `docker/`: System virtualization Dockerfiles.
- `simulator/`: CLI performance test simulator script.
- `docs/`: Academic project report (`REPORT.md`).
- `.htaccess`: Auto-configures Apache routing on Hostinger shared servers.

---

## 👥 Preseeded Login Accounts
All demo passwords are **`password123`**:

| Role | Login Email | Permission Level |
| :--- | :--- | :--- |
| **Admin** | `admin@hms.cloud` | Read aggregate stats (total patients, doctors, records) |
| **Doctor** | `doctor.smith@hms.cloud` | View assigned schedules, write diagnoses, upload scan files |
| **Patient**| `patient.doe@hms.cloud` | Book specialist slots, view diagnostics records, download scans |

---

## 🐳 Step-by-Step 1: Running Locally (Docker Virtualization)

Ensure you have **Docker Desktop** installed on your machine.

### Step 1: Launch Containers
Open your terminal, navigate to the docker directory, and spin up the virtualization stack:
```bash
# Navigate to docker folder
cd docker

# Build and launch containers
docker-compose up --build
```
This automatically boots a PHP Apache container on port `8080` (`http://localhost:8080`) and a MySQL server container.

### Step 2: Initialize Database and Demo Accounts
In a new terminal window, execute the composer dependencies installation and run database migrations inside the active container:
```bash
# Install framework dependencies
docker exec -it hms_laravel_app composer install

# Run database migrations and load seeder accounts
docker exec -it hms_laravel_app php artisan migrate --seed
```

### Step 3: Access the Portal
Go to **`http://localhost:8080`** in your browser. Click any of the **Demo Account buttons** at the bottom of the card to fill the form and log in instantly.

---

## ☁️ Step-by-Step 2: Deploying to Hostinger (Shared Hosting)

Hostinger shared hosting is perfect for live presentations. Since shared servers do not support command-line SSH inputs in lower tiers, the project is configured to let you set it up **entirely through the web browser**.

### Step 1: Create a Database
1. Log in to your Hostinger **hPanel**.
2. Go to **Databases** &rarr; **MySQL Databases** and create a new database. Keep note of the database name, username, and password.

### Step 2: Set Up Cloud Object Storage (Cloudinary - 100% Free)
1. Go to [Cloudinary](https://cloudinary.com/) and register a free account.
2. In your Cloudinary dashboard, copy your **Cloud Name**.
3. Go to **Settings (Gear Icon)** &rarr; **Upload** &rarr; scroll down to **Upload presets** and click **Add upload preset**.
   - Set Name to `hms_unsigned_preset`.
   - Set Signing Mode to **Unsigned**.
   - Click Save.

### Step 3: Configure Environment Variables
1. Open the `.env` file in the root of the project directory.
2. Update the database credentials to match Hostinger's credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_hostinger_database_name
   DB_USERNAME=your_hostinger_database_username
   DB_PASSWORD=your_hostinger_database_password
   ```
3. Update the Cloudinary settings:
   ```env
   CLOUDINARY_CLOUD_NAME=your_cloudinary_cloud_name
   CLOUDINARY_UPLOAD_PRESET=hms_unsigned_preset
   ```

### Step 4: Upload Code Files
1. Select all files in the `cloud_hospital_system` folder and compress them into a **`.zip`** archive (make sure to include hidden files like `.env` and `.htaccess`).
2. In Hostinger hPanel, go to **File Manager** &rarr; enter your domain's `public_html/` folder.
3. Upload the `.zip` archive and **extract** it directly into the `public_html/` folder.
4. *Note: The pre-configured `.htaccess` file in the root will automatically map incoming traffic from `yourdomain.com` into the `public/` directory safely.*

### Step 5: Initialize Database via the Browser
Go to your browser and access the custom migration path:
👉 **`https://yourdomain.com/run-migrations`**

This script executes the migrations and seeds the database directly from the web browser, outputting the SQL creation status. Click **"Go to Login"** to launch the portal.

---

## 📊 Step-by-Step 3: Running the Performance Load Test

You can benchmark the REST API directly using the PHP simulator:

```bash
# Run simulator: php simulator/simulate_load.php [target_url] [requests]
# Against local virtualized container:
php simulator/simulate_load.php http://localhost:8080 50

# Against your live Hostinger URL:
php simulator/simulate_load.php https://yourdomain.com 30
```
This authenticates a patient session, sends concurrent JSON payloads to the `/api/book-appointment` endpoint, and compiles a latency and throughput analysis in requests per second.
