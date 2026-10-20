#! /bin/bash -e
#
# Seed content for local development (idempotent). Run from the theme root:
#   bash dev-scripts/seed.sh
# Local only. Never point this at staging/production.
#

WP="wp --path=../../../"
TMP="${SEED_TMP:-$(mktemp -d)}"
mkdir -p "$TMP"

echo "Seeding pages..."

page_id() { # slug title [content] -> id (reuses existing page with slug)
    local id
    id=$($WP post list --post_type=page --name="$1" --post_status=any --field=ID | head -n1)
    if [ -z "$id" ]; then
        id=$($WP post create --post_type=page --post_status=publish --post_title="$2" --post_name="$1" --post_content="${3:-}" --porcelain)
    fi
    echo "$id"
}

HOME_ID=$(page_id home "Home" "<!-- wp:paragraph --><p>Sister Midnight is a community-owned music venue and radio station in Catford, South East London.</p><!-- /wp:paragraph -->")
ABOUT_ID=$(page_id about "About" "<!-- wp:paragraph --><p>Sister Midnight is a co-operative venue, bar and radio station run by and for its community.</p><!-- /wp:paragraph -->")
VENUE_ID=$(page_id venue "Venue" "<!-- wp:paragraph --><p>Find us on Brookdale Road, Catford.</p><!-- /wp:paragraph -->")
SUPPORT_ID=$(page_id support "Support" "<!-- wp:paragraph --><p>Help keep the lights on: become a member, donate or volunteer.</p><!-- /wp:paragraph -->")
NEWS_ID=$(page_id news "News")
MEMBER_ID=$(page_id membership "Membership")
CONTACT_ID=$(page_id contact "Contact")

$WP option update show_on_front page >/dev/null
$WP option update page_on_front "$HOME_ID" >/dev/null
$WP option update page_for_posts "$NEWS_ID" >/dev/null
$WP option update timezone_string Europe/London >/dev/null

echo "Seeding menus..."

menu_reset() { # name location
    $WP menu list --fields=name --format=csv | grep -qx "$1" || $WP menu create "$1" >/dev/null
    for i in $($WP menu item list "$1" --format=ids); do $WP menu item delete "$i" >/dev/null; done
    $WP menu location assign "$1" "$2" >/dev/null
}

menu_reset Primary header
$WP menu item add-post Primary "$ABOUT_ID" --title="About" >/dev/null
$WP menu item add-custom Primary "Events" "/whats-on/" >/dev/null
$WP menu item add-post Primary "$VENUE_ID" --title="Venue" >/dev/null
$WP menu item add-custom Primary "Radio" "https://radio.sistermidnight.org" >/dev/null
$WP menu item add-post Primary "$SUPPORT_ID" --title="Support" >/dev/null

menu_reset Footer footer-1
$WP menu item add-post Footer "$CONTACT_ID" --title="Contact us" >/dev/null
$WP menu item add-custom Footer "Subscribe" "/#newsletter" >/dev/null
$WP menu item add-post Footer "$MEMBER_ID" --title="Membership" >/dev/null
$WP menu item add-post Footer "$SUPPORT_ID" --title="Support Us" >/dev/null

echo "Generating placeholder images..."

COLORS=(c2410c 1d4e5f 7c3aed b45309 0f766e be185d 4d7c0f 1e40af)
for n in 1 2 3 4 5 6 7 8; do
    c=${COLORS[$((n - 1))]}
    [ -f "$TMP/poster-$n.jpg" ] || magick -size 800x1000 "gradient:#$c-#111111" -gravity center -fill white -pointsize 64 -annotate 0 "Poster $n" "$TMP/poster-$n.jpg"
done
for n in 1 2 3; do
    c=${COLORS[$((n + 2))]}
    [ -f "$TMP/news-$n.jpg" ] || magick -size 1200x800 "gradient:#$c-#111111" -gravity center -fill white -pointsize 64 -annotate 0 "News $n" "$TMP/news-$n.jpg"
done

echo "Seeding events, news and options..."

SEED_TMP="$TMP" $WP eval '
$tmp = getenv("SEED_TMP");
require_once ABSPATH . "wp-admin/includes/image.php";
require_once ABSPATH . "wp-admin/includes/file.php";
require_once ABSPATH . "wp-admin/includes/media.php";

function seed_find(string $key, string $type) {
    $p = get_posts(["post_type" => $type, "post_status" => "any", "meta_key" => "_seed_key", "meta_value" => $key, "numberposts" => 1, "fields" => "ids"]);
    return $p[0] ?? 0;
}
function seed_image(string $key, string $file, string $title): int {
    if ($id = seed_find($key, "attachment")) return $id;
    $id = media_handle_sideload(["name" => basename($file), "tmp_name" => $file], 0, $title);
    if (is_wp_error($id)) { fwrite(STDERR, $id->get_error_message() . "\n"); return 0; }
    update_post_meta($id, "_seed_key", $key);
    update_post_meta($id, "_wp_attachment_image_alt", $title);
    return $id;
}
function seed_copy(string $src): string { $d = $src . ".upload.jpg"; copy($src, $d); return $d; }

$term = term_exists("Sister Midnight", "location") ?: wp_insert_term("Sister Midnight", "location");
$term_id = is_array($term) ? (int) $term["term_id"] : (int) $term;

$events = [
  ["Late Night Jazz Jam", "Sister Midnight Presents", -55, "20:00", "buy", "Sister Midnight"],
  ["Dub Sessions", "Sister Midnight Presents", -31, "21:00", "buy", "Sister Midnight"],
  ["Community Choir Evening", "Catford Voices", -9, "18:30", "free", "Sister Midnight"],
  ["Records & Rye", "Sister Midnight Presents", 4, "19:00", "buy", "Sister Midnight"],
  ["Open Mic Night", "Sister Midnight Presents", 12, "19:30", "signup", "Sister Midnight"],
  ["Soul Revue", "Sister Midnight Presents", 24, "20:00", "buy", "Sister Midnight"],
  ["Radio Takeover", "Sister Midnight Radio", 38, "17:00", "free", "Sister Midnight"],
  ["Midnight Disco", "Sister Midnight Presents", 52, "22:00", "buy", "Sister Midnight"],
];
foreach ($events as $i => [$title, $presenter, $days, $time, $ticket, $venue]) {
    $n = $i + 1; $key = "event-$n";
    $id = seed_find($key, "event");
    if (!$id) {
        $id = wp_insert_post(["post_type" => "event", "post_status" => "publish", "post_title" => $title,
            "post_content" => "<!-- wp:paragraph --><p>An evening of music at Sister Midnight. Doors at $time.</p><!-- /wp:paragraph -->",
            "post_excerpt" => "$title at Sister Midnight."]);
        update_post_meta($id, "_seed_key", $key);
    }
    $date = (new DateTime("today", wp_timezone()))->modify(sprintf("%+d days", $days))->format("Y-m-d");
    update_field("presenter", $presenter, $id);
    update_field("start", "$date $time:00", $id);
    update_field("end_label", "late", $id);
    update_field("venue_name", $venue, $id);
    update_field("ticket_type", $ticket, $id);
    if ($ticket !== "free") update_field("ticket_link", ["title" => $ticket === "buy" ? "Buy Tickets" : "Sign up", "url" => "https://example.com/tickets/$n", "target" => "_blank"], $id);
    wp_set_object_terms($id, [$term_id], "location");
    if ($img = seed_image("poster-$n", seed_copy("$tmp/poster-$n.jpg"), "$title poster")) set_post_thumbnail($id, $img);
}

$news = [
  ["We are now a community benefit society", "Our share offer has closed, and the venue now belongs to its members."],
  ["Radio schedule: autumn shows", "New resident shows start on the station this month."],
  ["Thank you to our supporters", "A round-up of the funders and neighbours who made the refit possible."],
];
foreach ($news as $i => [$title, $excerpt]) {
    $n = $i + 1; $key = "news-$n";
    $id = seed_find($key, "post");
    if (!$id) {
        $id = wp_insert_post(["post_type" => "post", "post_status" => "publish", "post_title" => $title, "post_excerpt" => $excerpt,
            "post_date" => date("Y-m-d H:i:s", strtotime("-" . ($n * 6) . " days")),
            "post_content" => "<!-- wp:paragraph --><p>$excerpt</p><!-- /wp:paragraph -->"]);
        update_post_meta($id, "_seed_key", $key);
    }
    if ($img = seed_image("news-$n", seed_copy("$tmp/news-$n.jpg"), $title)) set_post_thumbnail($id, $img);
}

update_field("address", "Sister Midnight, Brookdale Road, Catford, SE6 4JY", "option");
update_field("footer_legal_text", "<p>Sister Midnight Ltd is a registered Community Benefit Society. All rights reserved.</p>", "option");
update_field("newsletter_heading", "Newsletter", "option");
update_field("newsletter_text", "Sign up for news, events and radio schedules from Sister Midnight.", "option");
update_field("radio_enabled", 1, "option");
update_field("station_id", "s35e4926a1", "option");
update_field("listen_url", "https://stream.radio.co/s35e4926a1/listen", "option");
' 

echo "Newsletter form..."
if $WP plugin is-active gravityforms 2>/dev/null && $WP help gf >/dev/null 2>&1; then
    FORM=$($WP gf form list --format=csv 2>/dev/null | grep -i ',Newsletter' | head -n1 | cut -d, -f1)
    [ -n "$FORM" ] || FORM=$($WP gf form create "Newsletter" --porcelain)
    $WP eval "update_field('newsletter_form_id', (int) '$FORM', 'option');"
else
    echo "Gravity Forms CLI not available: using placeholder newsletter_form_id=1 (see .docs/BUILD-DECISIONS.md)"
    $WP eval "update_field('newsletter_form_id', 1, 'option');"
fi

$WP rewrite flush >/dev/null
echo "Done."
