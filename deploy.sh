#!/bin/bash
#
# HP Accountants Theme - FTP Deploy Script
#
# Deploys the theme to A2 Hosting via FTP:
#   1. Deletes old backups (> 1 month)
#   2. Backs up current remote theme as hpaccountants-theme_old_YYYYMMDD
#   3. Uploads local theme files
#
# Usage: ./deploy.sh
#
# Prerequisites:
#   - lftp (auto-installs via Homebrew if missing)
#   - .deploy-config file with FTP credentials
#

set -euo pipefail

# ─── Config ───────────────────────────────────────────────────────────────────

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CONFIG_FILE="$SCRIPT_DIR/.deploy-config"

if [[ ! -f "$CONFIG_FILE" ]]; then
	echo "ERROR: Config file not found: $CONFIG_FILE"
	echo "Create .deploy-config with your FTP credentials (see .deploy-config in the repo)."
	exit 1
fi

source "$CONFIG_FILE"

# Validate required config
for var in FTP_HOST FTP_USER FTP_PASS REMOTE_THEMES_DIR REMOTE_THEME_NAME; do
	if [[ -z "${!var:-}" ]]; then
		echo "ERROR: $var is not set in $CONFIG_FILE"
		exit 1
	fi
done

FTP_PORT="${FTP_PORT:-21}"
BACKUP_PREFIX="${REMOTE_THEME_NAME}_old_"
TODAY=$(date +%Y%m%d)
BACKUP_NAME="${BACKUP_PREFIX}${TODAY}"

# ─── Dependencies ─────────────────────────────────────────────────────────────

if ! command -v lftp &>/dev/null; then
	echo "lftp is not installed. Installing via Homebrew..."
	if command -v brew &>/dev/null; then
		brew install lftp
	else
		echo "ERROR: lftp is required. Install it with: brew install lftp"
		exit 1
	fi
fi

# ─── Exclusions ───────────────────────────────────────────────────────────────

EXCLUDES=(
	".git/"
	".gitignore"
	".DS_Store"
	"node_modules/"
	".deploy-config"
	"deploy.sh"
	".idea/"
	".vscode/"
	"*.log"
	"*.sublime-project"
	"*.sublime-workspace"
	"docs/"
	".claude/"
)

EXCLUDE_ARGS=""
for pattern in "${EXCLUDES[@]}"; do
	EXCLUDE_ARGS="$EXCLUDE_ARGS --exclude-glob $pattern"
done

# ─── Calculate cutoff date (1 month ago) ──────────────────────────────────────

if [[ "$(uname)" == "Darwin" ]]; then
	CUTOFF=$(date -v-1m +%Y%m%d)
else
	CUTOFF=$(date -d '1 month ago' +%Y%m%d)
fi

# ─── Helper: run an lftp command ──────────────────────────────────────────────

run_lftp() {
	lftp -e "
		set ftp:ssl-allow yes
		set ssl:verify-certificate no
		open -u '$FTP_USER','$FTP_PASS' -p $FTP_PORT $FTP_HOST
		$1
		quit
	"
}

# ─── Deploy ───────────────────────────────────────────────────────────────────

echo "============================================"
echo "  HP Accountants Theme — FTP Deploy"
echo "============================================"
echo ""
echo "  Host:   $FTP_HOST"
echo "  User:   $FTP_USER"
echo "  Remote: $REMOTE_THEMES_DIR/$REMOTE_THEME_NAME"
echo "  Backup: $REMOTE_THEMES_DIR/$BACKUP_NAME"
echo "  Cutoff: Deleting backups older than $CUTOFF"
echo ""

read -rp "Proceed with deployment? [y/N] " confirm
if [[ "$confirm" != [yY] ]]; then
	echo "Deployment cancelled."
	exit 0
fi

echo ""
echo "Connecting to $FTP_HOST..."

# ── Step 1: Delete old backups (> 1 month) ────────────────────────────────────

echo ""
echo "── Step 1: Cleaning up old backups ──"

BACKUP_LIST=$(run_lftp "
	cd $REMOTE_THEMES_DIR
	cls -1 --sort=name ${BACKUP_PREFIX}*
" 2>/dev/null || true)

if [[ -n "$BACKUP_LIST" ]]; then
	DELETED=0
	while IFS= read -r dir; do
		dir="${dir%/}"
		[[ -z "$dir" ]] && continue

		# Extract YYYYMMDD date from directory name
		backup_date="${dir##*_old_}"

		if [[ "$backup_date" =~ ^[0-9]{8}$ ]] && [[ "$backup_date" -lt "$CUTOFF" ]]; then
			echo "  Deleting old backup: $dir ($backup_date)"
			run_lftp "cd $REMOTE_THEMES_DIR; rm -rf $dir"
			((DELETED++))
		else
			echo "  Keeping recent backup: $dir ($backup_date)"
		fi
	done <<< "$BACKUP_LIST"
	echo "  Removed $DELETED old backup(s)."
else
	echo "  No existing backups found."
fi

# ── Step 2: Backup current theme ─────────────────────────────────────────────

echo ""
echo "── Step 2: Backing up current theme ──"

THEME_EXISTS=$(run_lftp "
	cd $REMOTE_THEMES_DIR
	ls -d $REMOTE_THEME_NAME
" 2>/dev/null || true)

if [[ -n "$THEME_EXISTS" ]]; then
	# Remove existing backup for today if re-deploying same day
	TODAY_BACKUP_EXISTS=$(run_lftp "
		cd $REMOTE_THEMES_DIR
		ls -d $BACKUP_NAME
	" 2>/dev/null || true)

	if [[ -n "$TODAY_BACKUP_EXISTS" ]]; then
		echo "  Backup for today already exists ($BACKUP_NAME). Removing it..."
		run_lftp "cd $REMOTE_THEMES_DIR; rm -rf $BACKUP_NAME"
	fi

	echo "  Renaming $REMOTE_THEME_NAME → $BACKUP_NAME"
	run_lftp "cd $REMOTE_THEMES_DIR; mv $REMOTE_THEME_NAME $BACKUP_NAME"
	echo "  Backup created."
else
	echo "  No existing theme found on remote. Skipping backup."
fi

# ── Step 3: Upload theme ─────────────────────────────────────────────────────

echo ""
echo "── Step 3: Uploading theme ──"
echo "  Source: $SCRIPT_DIR"
echo "  Target: $REMOTE_THEMES_DIR/$REMOTE_THEME_NAME"
echo "  This may take a minute..."

(cd "$SCRIPT_DIR" && run_lftp "mirror --reverse --verbose --delete $EXCLUDE_ARGS . $REMOTE_THEMES_DIR/$REMOTE_THEME_NAME")

echo ""
echo "============================================"
echo "  Deploy complete!"
echo "============================================"
echo ""
echo "  Theme uploaded to: $REMOTE_THEMES_DIR/$REMOTE_THEME_NAME"
echo "  Backup saved as:   $REMOTE_THEMES_DIR/$BACKUP_NAME"
echo ""
echo "  Verify at: https://hpaccountants.com.au"
echo ""
