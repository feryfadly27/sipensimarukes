#!/bin/bash

# ============================================
# SIPENSIMARUKES - Start All Services
# ============================================

echo "=========================================="
echo "   SIPENSIMARUKES - Starting Services"
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

REPO_DIR="/workspaces/sipensimarukes"

# 0. Ensure git branch is main and synced with origin/main
echo -e "\n${YELLOW}[0/6] Syncing repository to main...${NC}"
cd "$REPO_DIR" || exit 1

if [ -n "$(git status --porcelain)" ]; then
    echo -e "${RED}✗ Working tree has uncommitted changes${NC}"
    echo "Please commit/stash changes first, then run this script again."
    exit 1
fi

if ! git fetch origin main; then
    echo -e "${RED}✗ Failed to fetch origin/main${NC}"
    exit 1
fi

if ! git checkout main; then
    echo -e "${RED}✗ Failed to checkout main${NC}"
    exit 1
fi

if ! git pull --ff-only origin main; then
    echo -e "${RED}✗ Failed to fast-forward pull main${NC}"
    echo "Resolve branch divergence manually, then run this script again."
    exit 1
fi

echo -e "${GREEN}✓ Repository is on main and up to date${NC}"

# Function to check if service is running
check_service() {
    if pgrep -x "$1" > /dev/null; then
        echo -e "${GREEN}✓ $2 is running${NC}"
        return 0
    else
        echo -e "${RED}✗ $2 is not running${NC}"
        return 1
    fi
}

# 1. Start MySQL Service
echo -e "\n${YELLOW}[1/6] Starting MySQL...${NC}"
if ! pgrep -x "mysqld" > /dev/null; then
    sudo service mysql start
    sleep 2
fi

# Fix socket directory permissions
sudo chmod 755 /var/run/mysqld 2>/dev/null

check_service "mysqld" "MySQL"

# 2. Check database connection
echo -e "\n${YELLOW}[2/6] Checking database connection...${NC}"

# Load credentials from .env
DB_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)
DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)

# Try to connect with app credentials
if mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1" > /dev/null 2>&1; then
    echo -e "${GREEN}✓ MySQL connection OK (user: $DB_USER)${NC}"
    echo -e "${GREEN}✓ Database $DB_NAME ready${NC}"
else
    # Fallback: try root and create user if needed
    echo -e "${YELLOW}Creating database user...${NC}"
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;" 2>/dev/null
    sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';" 2>/dev/null
    sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';" 2>/dev/null
    sudo mysql -e "FLUSH PRIVILEGES;" 2>/dev/null
    
    if mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1" > /dev/null 2>&1; then
        echo -e "${GREEN}✓ MySQL connection OK${NC}"
    else
        echo -e "${RED}✗ Cannot connect to MySQL${NC}"
        echo "Please check your .env database configuration"
        exit 1
    fi
fi

# 3. Run migrations if needed
echo -e "\n${YELLOW}[3/6] Running migrations...${NC}"
cd "$REPO_DIR"
php artisan migrate --force 2>/dev/null
echo -e "${GREEN}✓ Migrations completed${NC}"

# 4. Start Laravel Development Server
echo -e "\n${YELLOW}[4/6] Starting Laravel server...${NC}"

# Kill existing PHP server on port 8000 if any
pkill -f "php artisan serve" 2>/dev/null
fuser -k 8000/tcp 2>/dev/null
sleep 2

# Start server in background
nohup php artisan serve --host=0.0.0.0 --port=8000 > /tmp/laravel.log 2>&1 &
sleep 3

if pgrep -f "php artisan serve" > /dev/null; then
    echo -e "${GREEN}✓ Laravel server started on port 8000${NC}"
else
    echo -e "${RED}✗ Failed to start Laravel server${NC}"
    echo "Check /tmp/laravel.log for errors"
    exit 1
fi

# 5. Set Codespaces port visibility to public
echo -e "\n${YELLOW}[5/6] Setting port 8000 to public...${NC}"
if command -v gh > /dev/null 2>&1 && [ -n "$CODESPACE_NAME" ]; then
    if gh codespace ports visibility 8000:public -c "$CODESPACE_NAME" > /dev/null 2>&1; then
        echo -e "${GREEN}✓ Port 8000 is now PUBLIC${NC}"
    else
        echo -e "${RED}✗ Failed to set port visibility to public${NC}"
        echo "Run manually: gh codespace ports visibility 8000:public -c \"$CODESPACE_NAME\""
    fi
else
    echo -e "${YELLOW}! Skipped: not running in Codespaces or gh CLI unavailable${NC}"
fi

# Summary
echo -e "\n=========================================="
echo -e "${GREEN}   All services started successfully!${NC}"
echo "=========================================="
echo ""
echo "Access the application at:"
echo "  Local:      http://localhost:8000"
echo "  Codespaces: Port 8000 set to public (if running in Codespaces)"
echo ""
echo "To view Laravel logs: tail -f /tmp/laravel.log"
echo "To stop server: pkill -f 'php artisan serve'"
echo ""
