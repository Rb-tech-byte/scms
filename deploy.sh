#!/usr/bin/env bash
# ==============================================================================
# Short Course Management System (SCMS) - Production Deployment Script
# Institute of Education and Management Technologies (IEMT)
# The Open University of Tanzania
#
# Author & Lead Developer: Renatus Bernardo <bernadorenatus32@gmail.com>
# ==============================================================================
# Usage:
#   ./deploy.sh                  Standard automated deployment
#   ./deploy.sh --skip-seed      Deploy without running baseline seeders
#   ./deploy.sh --skip-build     Deploy without rebuilding npm frontend assets
#   ./deploy.sh --branch=staging Deploy a specific git branch
# ==============================================================================

set -Eeuo pipefail

# Color indicators for clean terminal logging
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

BRANCH="main"
SKIP_SEED=false
SKIP_BUILD=false
DEPLOY_START=$(date +%s)

# Parse command line flags
for arg in "$@"; do
    case $arg in
        --skip-seed)
            SKIP_SEED=true
            shift
            ;;
        --skip-build)
            SKIP_BUILD=true
            shift
            ;;
        --branch=*)
            BRANCH="${arg#*=}"
            shift
            ;;
        *)
            ;;
    esac
done

log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
}

# Trap unexpected errors and ensure application is not left in maintenance mode
cleanup_on_error() {
    error "Deployment encountered a fatal error on line $1. Attempting recovery..."
    if command -v php >/dev/null 2>&1 && [ -f "artisan" ]; then
        php artisan up || true
    fi
    exit 1
}
trap 'cleanup_on_error $LINENO' ERR

log "Starting SCMS Automated Deployment on branch [${BRANCH}]..."

# 1. Resolve PHP and Composer binaries
PHP_BIN="php"
if [ -x "/Applications/MAMP/bin/php/php8.3.30/bin/php" ]; then
    PHP_BIN="/Applications/MAMP/bin/php/php8.3.30/bin/php"
fi

COMPOSER_BIN="composer"
if [ -x "/Applications/MAMP/bin/php/composer" ]; then
    COMPOSER_BIN="${PHP_BIN} /Applications/MAMP/bin/php/composer"
elif [ -f "./composer.phar" ]; then
    COMPOSER_BIN="${PHP_BIN} ./composer.phar"
fi

log "Using PHP runtime: $(${PHP_BIN} -r 'echo PHP_VERSION;')"

# 2. Activate Maintenance Mode
log "Activating maintenance mode..."
if ${PHP_BIN} artisan --version >/dev/null 2>&1; then
    ${PHP_BIN} artisan down --retry=60 || warn "Application already down or artisan offline."
fi

# 3. Fetch latest codebase from Git repository
log "Pulling latest changes from Git [origin/${BRANCH}]..."
git fetch origin "${BRANCH}"
git reset --hard "origin/${BRANCH}"
success "Codebase synchronized with commit $(git rev-parse --short HEAD)."

# 4. Install & Optimize Composer Dependencies
log "Installing production Composer dependencies..."
${COMPOSER_BIN} install --no-dev --prefer-dist --optimize-autoloader --no-interaction --quiet
success "Composer packages installed and autoloader optimized."

# 5. Compile Front-End Production Assets (if npm is available)
if [ "$SKIP_BUILD" = false ]; then
    if command -v npm >/dev/null 2>&1 && [ -f "package.json" ]; then
        log "Compiling production assets with Vite..."
        npm ci --prefer-offline --no-audit || npm install --no-audit
        npm run build
        success "Front-end assets compiled successfully."
    else
        log "npm not detected or --skip-build passed. Skipping asset build."
    fi
fi

# 6. Execute Database Migrations
log "Executing database migrations..."
${PHP_BIN} artisan migrate --force
success "Database migrations executed successfully."

# 7. Execute Prior Data Seed (Roles, Permissions, Reference Data, Superuser)
if [ "$SKIP_SEED" = false ]; then
    log "Executing baseline prior-data seeding (ProductionInitialSeeder)..."
    ${PHP_BIN} artisan db:seed --class=ProductionInitialSeeder --force
    success "Baseline reference data and compliant administrative credentials verified."
else
    log "Skipping database seeding as requested (--skip-seed)."
fi

# 8. Create Storage Symlink
log "Verifying public storage symlink..."
${PHP_BIN} artisan storage:link || true

# 9. Clear and Cache Application Configurations
log "Rebuilding application caches..."
${PHP_BIN} artisan config:clear
${PHP_BIN} artisan config:cache
${PHP_BIN} artisan route:clear
${PHP_BIN} artisan route:cache
${PHP_BIN} artisan view:clear
${PHP_BIN} artisan view:cache
${PHP_BIN} artisan event:cache || true
success "Configuration, routes, and views cached for high performance."

# 10. Ensure Writable Directory Permissions
log "Configuring storage and cache directory permissions..."
find storage bootstrap/cache -type d -exec chmod 775 {} + 2>/dev/null || true
find storage bootstrap/cache -type f -exec chmod 664 {} + 2>/dev/null || true
if id "www-data" >/dev/null 2>&1; then
    chown -R www-data:www-data storage bootstrap/cache || true
fi

# 11. Restart Workers & Clear Temporary Caches
log "Restarting background queues..."
${PHP_BIN} artisan queue:restart || true

# 12. Deactivate Maintenance Mode
log "Deactivating maintenance mode..."
${PHP_BIN} artisan up

DEPLOY_END=$(date +%s)
TOTAL_TIME=$((DEPLOY_END - DEPLOY_START))

echo -e "\n=================================================================="
echo -e "${GREEN}  SCMS DEPLOYMENT COMPLETED SUCCESSFULLY IN ${TOTAL_TIME} SECONDS!${NC}"
echo -e "  Commit: $(git rev-parse --short HEAD) - $(git log -1 --pretty=%B | head -n 1)"
echo -e "  Time:   $(date '+%Y-%m-%d %H:%M:%S')"
echo -e "==================================================================\n"
