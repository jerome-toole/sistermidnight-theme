#! /bin/bash -e

#
#   Output colours
#

GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

# -----------------------------------------------------------------------------
# Gust Site Setup Script
#
# This script clears the terminal, displays a formatted header for the Gust Site
# Setup, and prompts the user to confirm whether to run the setup process.
# -----------------------------------------------------------------------------
clear
# Display script description

echo -e "${GREEN}"
echo "+----------------------+"
echo "| Gust Site Setup      |"
echo "+----------------------+"
echo -e "${NC}"

echo -e "${BLUE}This script will configure your WordPress site for development by enabling debugging, adding the ACF Pro licence key, removing default content, setting up language and permalinks, disabling comments, and activating plugins and the current theme.${NC}"
echo

read -r -p $'\e[34mRun setup? (Y/n) \e[0m' run

if [[ "$run" =~ ^[Nn]$ ]]; then
    exit
fi

echo -e "${GREEN}Running setup...${NC}"

printf '\n'
echo "Enabling debugging..."
wp config set WP_DEBUG true --raw
wp config set WP_DEBUG_LOG true --raw
wp config set WP_DEBUG_DISPLAY false --raw
wp config set WP_ENVIRONMENT_TYPE "development"
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Adding ACF Pro licence key..."
ACF_PRO_KEY=$(php -r '$a = json_decode((string) @file_get_contents("auth.json"), true); echo $a["http-basic"]["connect.advancedcustomfields.com"]["username"] ?? "";')
if [ -n "$ACF_PRO_KEY" ] && [ "$ACF_PRO_KEY" != "ACF_PRO_KEY" ]; then
    wp config set ACF_PRO_LICENSE "$ACF_PRO_KEY" --quiet
    echo -e "${GREEN}Done!${NC}"
else
    echo "No ACF Pro key found in auth.json — skipping."
fi

printf '\n'
echo "Deleting default plugins, content and older themes..."

# Use || true to prevent script exit if items don't exist
wp post delete $(wp post list --post_type=page --posts_per_page=1 --post_status=publish --pagename="sample-page" --field=ID --format=ids 2>/dev/null) --quiet 2>/dev/null || true
wp post delete $(wp post list --post_type=post --posts_per_page=1 --post_status=publish --name="hello-world" --field=ID --format=ids 2>/dev/null) --quiet 2>/dev/null || true

wp plugin delete akismet hello --quiet 2>/dev/null || true
wp theme delete twentytwentytwo twentytwentythree twentytwentyfour --quiet 2>/dev/null || true

echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Discouraging search engines..."
wp option update blog_public 0 --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Activating English (UK) language..."
wp language core install en_GB --quiet
wp site switch-language en_GB --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Disabling comments/pings..."

IDS=$(wp post list --post_type=page --format=ids 2>/dev/null)
[ -n "$IDS" ] && echo "$IDS" | xargs wp post update --comment_status=closed --ping_status=closed --quiet || true

wp option update default_pingback_flag "" --quiet
wp option update default_ping_status "" --quiet
wp option update default_comment_status "" --quiet
wp option update require_name_email 1 --quiet
wp option update comment_registration 1 --quiet
wp option update close_comments_days_old 0 --quiet
wp option update show_comments_cookies_opt_in "" --quiet
wp option update comments_notify 1 --quiet
wp option update moderation_notify 1 --quiet
wp option update comment_moderation 1 --quiet
wp option update comment_previously_approved "" --quiet
wp option update comment_max_links 0 --quiet

echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Setting default permalink structure..."
wp rewrite structure '/%postname%/' --hard --quiet
wp rewrite flush --hard --quiet
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Creating homepage..."
if ! wp post list --post_type=page --post_status=publish --pagename=home --field=ID --format=ids --quiet 2>/dev/null | grep -q .; then
    HOME_ID=$(wp post create --post_type=page --post_title=Home --post_status=publish --menu_order=-1 --porcelain)
    wp option update show_on_front "page" --quiet
    wp option update page_on_front "$HOME_ID" --quiet
fi
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Activating all plugins..."
wp plugin activate --all --skip-themes
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Activating theme..."
wp theme activate "$(basename "$(pwd)")"
echo -e "${GREEN}Done!${NC}"

printf '\n'
echo "Setting up navigation menu..."
HEADER_MENU_ID=$(wp menu location assignment list --fields=menu_id --location=header --format=ids 2>/dev/null || true)
if [ -z "$HEADER_MENU_ID" ]; then
    MENU_ID=$(wp menu create "Main Menu" --porcelain)
    HOME_PAGE_ID=$(wp option get page_on_front 2>/dev/null || true)
    if [ -n "$HOME_PAGE_ID" ] && [ "$HOME_PAGE_ID" != "0" ]; then
        wp menu item add-post "$MENU_ID" "$HOME_PAGE_ID" --title="Home" --quiet
    else
        wp menu item add-custom "$MENU_ID" "Home" "/" --quiet
    fi
    SITE_URL=$(wp option get siteurl 2>/dev/null)
    wp menu item add-custom "$MENU_ID" "Dev Kit" "${SITE_URL}/_dev/" --quiet
    wp menu location assign "$MENU_ID" header --quiet
    echo -e "${GREEN}Done!${NC}"
else
    echo "Menu already assigned to header location — skipping."
fi


printf '\n'
echo "█ █▄░█ █▀ ▀█▀ ▄▀█ █░░ █░░   █▀▀ █▀█ █▀▄▀█ █▀█ █░░ █▀▀ ▀█▀ █▀▀"
echo "█ █░▀█ ▄█ ░█░ █▀█ █▄▄ █▄▄   █▄▄ █▄█ █░▀░█ █▀▀ █▄▄ ██▄ ░█░ ██▄"
printf '\n'
