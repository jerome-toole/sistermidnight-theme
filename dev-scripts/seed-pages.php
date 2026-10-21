<?php
// Contact and Membership page content (idempotent). Run via: wp eval-file dev-scripts/seed-pages.php
// Block markup helpers.
function sp_section(array $data, string $inner): string {
    $d = [];
    foreach ($data as $k => $v) { $d["section_$k"] = $v; $d["_section_$k"] = "field_section_$k"; }
    $json = wp_json_encode(["name" => "theme/section", "data" => $d, "mode" => "preview"], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return "<!-- wp:theme/section $json -->\n$inner\n<!-- /wp:theme/section -->";
}
function sp_p(string $html): string { return "<!-- wp:paragraph -->\n<p>$html</p>\n<!-- /wp:paragraph -->"; }
function sp_h(string $text, string $style = "type-h3"): string {
    return "<!-- wp:heading {\"level\":3,\"className\":\"is-style-$style\"} -->\n<h3 class=\"wp-block-heading is-style-$style\">$text</h3>\n<!-- /wp:heading -->";
}
function sp_group(string $inner): string {
    return "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\">$inner</div>\n<!-- /wp:group -->";
}
function sp_set(int $id, string $content, string $intro, string $illustration): void {
    wp_update_post(["ID" => $id, "post_content" => wp_slash($content)]);
    update_field("intro_text", $intro, $id);
    update_field("header_media", "illustration", $id);
    update_field("header_illustration", $illustration, $id);
}
function sp_page(string $slug): int {
    $p = get_posts(["post_type" => "page", "name" => $slug, "post_status" => "any", "numberposts" => 1, "fields" => "ids"]);
    return $p[0] ?? 0;
}

// Contact
if ($id = sp_page("contact")) {
    $general = sp_group(sp_h("General") . "\n" . sp_p('→ <a href="mailto:info@sistermidnight.org">info@sistermidnight.org</a>'));
    $radio = sp_group(sp_h("Radio") . "\n" . sp_p('→ <a href="mailto:radio@sistermidnight.org">radio@sistermidnight.org</a>'));
    $content = sp_section(
        ["label" => "Email us", "layout" => "two-column", "background" => "stone-beige", "media" => "none"],
        "$general\n$radio"
    );
    sp_set($id, $content, "<p>Get in touch with the Sister Midnight team by email.</p>", "disco-ball");
}

// Membership
if ($id = sp_page("membership")) {
    $about = sp_h("Become a member", "type-h4") . "\n"
        . sp_p("We’ve now secured a 16-year lease on the historic Brookdale Club and are transforming it into a grassroots music hub with a 250-capacity venue, café/bar, and rehearsal &amp; recording studios.") . "\n"
        . sp_p("Renovations began in December 2025, and to fund the next stage we need to raise £25,000 by the end of March 2026. Community shares start from £25, giving you democratic ownership and a real say in the venue’s future.") . "\n"
        . sp_p("Thanks to Music Venue Trust and Co-operatives UK, every £100 invested will be <strong>match-funded up to £25,000</strong>.") . "\n"
        . sp_p("<strong>Buy shares. Become a member. Build Sister Midnight with us.</strong>");
    // The Business Plan / Share Offer Document / Society Rules buttons are left out until their documents have URLs.
    $video = "<!-- wp:embed {\"url\":\"https://www.youtube.com/watch?v=eb0P9P8vPvk\",\"type\":\"video\",\"providerNameSlug\":\"youtube\",\"responsive\":true,\"className\":\"wp-embed-aspect-16-9 wp-has-aspect-ratio\"} -->\n<figure class=\"wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio\"><div class=\"wp-block-embed__wrapper\">\nhttps://www.youtube.com/watch?v=eb0P9P8vPvk\n</div></figure>\n<!-- /wp:embed -->";

    $faq = [
        ["Who can become a member?", "<p>Anyone can become a member of Sister Midnight by purchasing Community Shares. As a member, you’ll be a co-owner of Sister Midnight along with our other members and will have voting rights on important decisions.</p>"],
        ["What are Community Shares?", "<p>Community shares are withdrawable shares that cannot be sold, traded or transferred between members, unlike shares in a typical company. All members are entitled to one vote - regardless of how many shares they hold. Members can be paid interest on their shares, and also withdraw their shareholding, along with any interest accrued. This is subject to the approval of the board.</p>"],
        ["Please read the risks!", "<p>There is a risk that you could lose some or all of the money you invest. You have no right to compensation from the Financial Services Compensation Scheme, nor any right of complaint to the Financial Ombudsman Service. Please ensure that you understand the risks associated with investment and don’t invest any money you can’t afford to lose.</p>"],
    ];
    $fd = ["faq_heading" => "FAQ", "_faq_heading" => "field_faq_heading", "faq_items" => count($faq), "_faq_items" => "field_faq_items"];
    foreach ($faq as $i => [$q, $a]) {
        $fd["faq_items_{$i}_question"] = $q; $fd["_faq_items_{$i}_question"] = "field_faq_item_question";
        $fd["faq_items_{$i}_answer"] = $a;   $fd["_faq_items_{$i}_answer"] = "field_faq_item_answer";
    }
    $faq_block = "<!-- wp:theme/faq " . wp_json_encode(["name" => "theme/faq", "data" => $fd, "mode" => "preview"], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . " /-->";

    // Legacy photos; keep only those whose files exist locally (others point at the old staging host).
    $gallery_ids = [37, 38, 39, 40, 41, 42, 43, 44];
    $gallery_ids = array_values(array_filter($gallery_ids, fn($g) => get_post_type($g) === "attachment" && str_starts_with((string) wp_get_attachment_url($g), home_url()) && file_exists(get_attached_file($g))));
    $gd = ["gallery_label" => "", "_gallery_label" => "field_gallery_label", "gallery_images" => $gallery_ids, "_gallery_images" => "field_gallery_images"];
    $gallery = "<!-- wp:theme/gallery " . wp_json_encode(["name" => "theme/gallery", "data" => $gd, "align" => "full", "mode" => "auto"]) . " /-->";

    $content = sp_section(["label" => "Community shares", "layout" => "text-right", "background" => "dark-umber", "media" => "none"], $about . "\n" . $video)
        . "\n\n" . sp_section(["label" => "Questions", "layout" => "text-right", "background" => "stone-beige", "media" => "none"], $faq_block)
        . "\n\n" . $gallery;
    sp_set($id, $content, "<p>Sister Midnight is a community-owned co-op opening a new live music venue in Catford. Since 2018, we’ve championed emerging local artists and, after becoming a co-op in 2021, over 1,100 members have helped raise £365,000 in community shares.</p><p><strong>Our match-funded crowdfunder opens 31.1.26</strong></p>", "record-heart");
}
