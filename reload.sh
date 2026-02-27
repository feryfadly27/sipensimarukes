#!/bin/bash

# ============================================
# SIPENSIMARUKES - Reload/Clear All Caches
# ============================================

echo "=========================================="
echo "   SIPENSIMARUKES - Reloading System"
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

cd /workspaces/sipensimarukes

# 1. Clear application cache
echo -e "\n${YELLOW}[1/7] Clearing application cache...${NC}"
php artisan cache:clear 2>/dev/null
echo -e "${GREEN}✓ Application cache cleared${NC}"

# 2. Clear config cache
echo -e "\n${YELLOW}[2/7] Clearing config cache...${NC}"
php artisan config:clear 2>/dev/null
echo -e "${GREEN}✓ Config cache cleared${NC}"

# 3. Clear route cache
echo -e "\n${YELLOW}[3/7] Clearing route cache...${NC}"
php artisan route:clear 2>/dev/null
echo -e "${GREEN}✓ Route cache cleared${NC}"

# 4. Clear view cache
echo -e "\n${YELLOW}[4/7] Clearing view cache...${NC}"
php artisan view:clear 2>/dev/null
echo -e "${GREEN}✓ View cache cleared${NC}"

# 5. Clear compiled classes
echo -e "\n${YELLOW}[5/7] Clearing compiled files...${NC}"
php artisan clear-compiled 2>/dev/null
echo -e "${GREEN}✓ Compiled files cleared${NC}"

# 6. Regenerate autoload files
echo -e "\n${YELLOW}[6/7] Regenerating autoload files...${NC}"
composer dump-autoload -o --quiet 2>/dev/null
echo -e "${GREEN}✓ Autoload files regenerated${NC}"

# 7. Run new migrations if any
echo -e "\n${YELLOW}[7/7] Running new migrations...${NC}"
php artisan migrate --force 2>/dev/null
echo -e "${GREEN}✓ Migrations checked${NC}"

# Optional: Restart Laravel server
echo ""
read -p "Restart Laravel server? (y/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "\n${YELLOW}Restarting Laravel server...${NC}"
    pkill -f "php artisan serve" 2>/dev/null
    sleep 1
    nohup php artisan serve --host=0.0.0.0 --port=8000 > /tmp/laravel.log 2>&1 &
    sleep 2
    
    if pgrep -f "php artisan serve" > /dev/null; then
        echo -e "${GREEN}✓ Laravel server restarted${NC}"
    else
        echo -e "${RED}✗ Failed to restart server${NC}"
    fi
fi

# Summary
echo -e "\n=========================================="
echo -e "${GREEN}   System reloaded successfully!${NC}"
echo "=========================================="
echo ""
echo "All caches have been cleared."
echo "Your changes should now be reflected."
echo ""
