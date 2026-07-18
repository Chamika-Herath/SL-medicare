#!/bin/bash

# =========================================================================
# Laravel Cloud HMS - Automated AWS EC2 Deployment Script
# Targets: Ubuntu 22.04 LTS / 24.04 LTS on AWS EC2 (t2.micro / t3.micro)
# =========================================================================

# Ensure the script is run with root privileges
if [ "$EUID" -ne 0 ]; then
  echo "Error: Please run this script with sudo or as root."
  exit 1
fi

echo "================================================================="
echo " Starting AWS EC2 Installation & Deployment for SL-medicare"
echo "================================================================="

# 1. Update and Upgrade System packages
echo "[Step 1/6] Updating system packages..."
apt update && apt upgrade -y

# 2. Install Docker & Docker Compose
echo "[Step 2/6] Installing Docker & Docker Compose..."
apt install -y ca-certificates curl gnupg lsb-release

mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

apt update
apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Verify Docker installation
if ! command -v docker &> /dev/null; then
    echo "Error: Docker installation failed."
    exit 1
fi
echo "Docker and Docker Compose installed successfully."

# 3. Install Nginx and Certbot (for reverse proxy & free HTTPS)
echo "[Step 3/6] Installing Nginx and Certbot..."
apt install -y nginx certbot python3-certbot-nginx

# Configure Nginx as a Reverse Proxy to Docker port 8080
echo "[Step 4/6] Configuring Nginx Reverse Proxy..."
cat > /etc/nginx/sites-available/hms << 'EOF'
server {
    listen 80;
    server_name _; # Change this to your domain name later for SSL

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF

# Enable Nginx site and disable default site
ln -sf /etc/nginx/sites-available/hms /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
systemctl restart nginx

# 4. Environment File Setup
echo "[Step 5/6] Initializing Laravel environment configurations..."
if [ -d "docker" ]; then
  cd docker
else
  echo "Error: Could not locate the 'docker' directory. Make sure you are in the project root."
  exit 1
fi

# Ensure a .env file exists inside the root (copied from .env.example)
if [ ! -f "../.env" ]; then
  echo "Copying .env.example to .env..."
  cp ../.env.example ../.env
  # Update environment variables inside .env for EC2 environment
  sed -i 's/APP_ENV=local/APP_ENV=production/' ../.env
  sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' ../.env
  sed -i 's/APP_URL=http:\/\/localhost/APP_URL=http:\/\/localhost:8080/' ../.env
fi

# 5. Build and Launch Containers
echo "[Step 6/6] Launching Docker Containers..."
docker compose up -d --build

# Set permissions for logs, sessions, and cache
echo "Setting permissions for storage and cache directories..."
chmod -R 777 ../storage ../bootstrap/cache

# Wait for database container boot-up
echo "Waiting for database to initialize (15 seconds)..."
sleep 15

# Install Composer dependencies inside active container
echo "Running composer installation inside container..."
docker exec -i hms_laravel_app composer install --no-dev --optimize-autoloader

# Generate APP_KEY if not set
echo "Generating Laravel encryption key..."
docker exec -i hms_laravel_app php artisan key:generate --force

# Clear and cache configurations
docker exec -i hms_laravel_app php artisan config:cache
docker exec -i hms_laravel_app php artisan route:cache
docker exec -i hms_laravel_app php artisan view:cache

# Execute migrations and seed accounts
echo "Running database migrations & seeder scripts..."
docker exec -i hms_laravel_app php artisan migrate:fresh --seed --force

echo "================================================================="
echo " Deployment Completed Successfully!"
echo " Access your portal at http://YOUR_EC2_PUBLIC_IP"
echo "================================================================="
echo ""
echo "To secure your site with HTTPS later, point your domain to this IP and run:"
echo "sudo certbot --nginx"
