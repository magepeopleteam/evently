<?php
/**
 * Evently canonical demo dataset.
 *
 * Two jobs read from this single source, so the theme never shows one
 * "fake homepage" dataset and imports a different "real demo content"
 * dataset:
 *   1. Homepage/section template-parts use it as a graceful fallback when
 *      no real events exist yet (fresh install, booking plugin inactive).
 *   2. The demo importer (inc/demo-import/importer.php, brief §28) turns
 *      the same arrays into real `mep_events` posts (or plain posts if the
 *      booking plugin isn't installed) so "preview" and "imported" content
 *      are the same content, not two different demos.
 *
 * Images referenced here are hotlinked Unsplash photos carried over from
 * the theme's own Figma source design (Unsplash License permits commercial
 * use, no attribution required). Before a real ThemeForest submission,
 * replace these with the vendor's own bundled/licensed photography — see
 * docs/demo-content.md.
 *
 * Every event also carries a `date_type` of 'fixed', 'particular', or
 * 'recurring' (a deliberate mix — see docs/demo-content.md) plus whichever
 * of `extra_dates` / `recurrence` that type needs. These are the theme's
 * own abstraction, not raw plugin postmeta — `importer.php` is what
 * translates them into the real `mep_*` meta keys the booking plugin
 * expects, so this file never has to guess plugin internals.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The 8 realistic demo events (brief §29 — exact names as specified).
 *
 * @return array[] Array of arrays shaped like evently_normalize_event()'s input,
 *                  plus a few importer-only keys (excerpt, description, vibe,
 *                  city, country, gallery_files, faq, timeline, date_type).
 */
function evently_demo_events() {
	static $events = null;

	if ( null !== $events ) {
		return $events;
	}

	$events = array(
		array(
			'id'          => 'demo-1',
			'start_date'  => '2026-08-24', // ISO date for import meta — date_full below is display-only (its en-dash range isn't reliably machine-parseable).
			'title'       => __( 'Summer Music Festival', 'evently' ),
			'excerpt'     => __( 'Three days of live music across five stages, headlined by international touring acts.', 'evently' ),
			'description' => __( "Summer Music Festival returns to Army Stadium for three full days of live music across five purpose-built stages, bringing together international touring acts and the region's most exciting rising talent under one roof. From the moment the gates open each afternoon, the stadium grounds transform into a self-contained festival city — a vendor village lined with more than twenty food stalls, pop-up merchandise booths from partner brands, and rest areas shaded from the summer sun.\n\nThe lineup spans genres deliberately: the Main Stage carries the big international headliners each night, while the Indie Stage, Bass Camp, Acoustic Lounge and Discovery Stage give festival-goers room to wander between sounds rather than commit to one genre for three days straight. Set times are staggered across all five stages so there's always something to walk to, and the schedule leaves generous gaps for food, rest, and the inevitable friend-finding that happens in a crowd this size.\n\nTicket tiers range from single-day general admission to a three-day VIP pass that includes a dedicated viewing platform, an air-conditioned lounge, and priority entry lanes — useful given past years have seen lines form well before doors. Whichever tier you choose, tickets are checked digitally at the gate, so keep your QR code accessible on your phone or printed as a backup.\n\nPractically: gates open at 4:00 PM each day and the last sets wrap by half past midnight, with a hard curfew enforced by the venue. Outside food, drink, and professional cameras are not permitted, though phones and point-and-shoot cameras are fine. Free water refill stations are set up throughout the grounds, and a first-aid tent sits near the Main Stage entrance for the full run of the festival.\n\nWhether you're coming for one headliner or all three days, Summer Music Festival is built around the idea that a festival should feel easy — clear signage, real shade, and a lineup wide enough that everyone in your group finds a stage they love.", 'evently' ),
			'category'    => __( 'Music', 'evently' ),
			'category_badge' => 'MUSIC',
			'vibe'        => array( 'Music', 'Travel' ),
			'date_label'  => 'AUG 24',
			'date_full'   => __( 'Aug 24–26, 2026', 'evently' ),
			'time'        => '18:30',
			'location'    => __( 'Dhaka, Bangladesh', 'evently' ),
			'venue'       => __( 'Army Stadium', 'evently' ),
			'city'        => 'Dhaka',
			'country'     => 'Bangladesh',
			'price'       => 49,
			'price_label' => __( 'From $49', 'evently' ),
			'rating'      => 4.8,
			'image_url'   => 'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-1-summer-music-festival.jpg',
			'organizer'   => __( 'Evently Live', 'evently' ),
			'date_type'   => 'fixed',
			'event_end_date_offset' => 2,
			'gallery_files' => array( 'demo-1-summer-music-festival.jpg', 'hero-concert-crowd.jpg', 'featured-music-festival.jpg', 'category-concerts.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'Can I bring my own food and drink?', 'evently' ), 'a' => __( "No outside food or drink is allowed inside the venue; the vendor village has offerings from 20+ local restaurants and licensed bars.", 'evently' ) ),
				array( 'q' => __( 'Is there camping on site?', 'evently' ), 'a' => __( "There is no on-site camping at Army Stadium, but we've partnered with nearby hotels for discounted festival-goer rates, with details emailed after purchase.", 'evently' ) ),
				array( 'q' => __( 'What happens if it rains?', 'evently' ), 'a' => __( "The festival goes ahead rain or shine; only an extreme weather warning from local authorities would trigger a schedule change, which we'd announce by email and on-site.", 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '16:00', 'title' => __( 'Gates Open', 'evently' ), 'desc' => __( 'Gates open, with the vendor village and art installations open across the stadium grounds.', 'evently' ) ),
				array( 'time' => '17:00', 'title' => __( 'Opening Acts', 'evently' ), 'desc' => __( 'Regional up-and-coming artists open each of the five stages.', 'evently' ) ),
				array( 'time' => '19:00', 'title' => __( 'Main Stage: Rising Headliners', 'evently' ), 'desc' => __( "Tonight's supporting headliners take the Main Stage.", 'evently' ) ),
				array( 'time' => '21:00', 'title' => __( 'International Headliner Set', 'evently' ), 'desc' => __( "The night's international touring headliner performs a full set.", 'evently' ) ),
				array( 'time' => '23:00', 'title' => __( 'Late Night DJ Sets', 'evently' ), 'desc' => __( 'DJ sets take over the Bass Camp and Acoustic Lounge stages.', 'evently' ) ),
				array( 'time' => '00:30', 'title' => __( 'Curfew — Gates Close', 'evently' ), 'desc' => __( 'Venue curfew; all stages close for the night.', 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-2',
			'start_date'  => '2026-08-24',
			'title'       => __( 'Future Business Summit', 'evently' ),
			'excerpt'     => __( 'Founders, investors and operators share what is actually working right now.', 'evently' ),
			'description' => __( "Future Business Summit brings founders, operators, and investors together for a single, densely packed day at the Pan Pacific Sonargaon — built less around big-stage inspiration and more around what's actually working right now for people building companies in this region.\n\nThe day opens with registration and coffee at 9:00 AM, followed by a keynote that sets the tone: no recycled advice, just a direct look at where funding, hiring, and growth stand today. From there, the morning moves into a panel of investors and operators discussing what's changed in how deals get done over the past year, followed by open Q&A rather than a scripted format.\n\nLunch is where the summit earns its reputation — it's structured as a working lunch with assigned table topics such as fundraising, go-to-market, hiring your first ten people, and international expansion, so conversations start with something in common instead of small talk. Attendees consistently rate the lunch sessions as valuable as the main stage.\n\nThe afternoon splits into breakout sessions across three rooms, each capped at a smaller group size to keep discussion genuinely two-way. Past breakouts have covered pricing strategy, cap table mistakes, and building a remote-first culture. The day closes with a fireside chat and an informal networking hour with drinks, giving people time to follow up on conversations from earlier in the day before heading home.\n\nAll main-stage sessions are recorded and shared with ticket holders afterward, though breakout sessions are deliberately kept off the record to encourage candid discussion. Business casual is the norm — most attendees dress the way they would for a client meeting, not a black-tie gala.\n\nRegistration includes a printed attendee directory ahead of the event, so you can look up who else is coming and line up a few conversations before you even arrive. Whether you're pre-seed and looking for your first real network, or already running a growing team and chasing the next stage of growth, Future Business Summit is built to make one day worth clearing your calendar for.", 'evently' ),
			'category'    => __( 'Conference', 'evently' ),
			'category_badge' => 'CONFERENCE',
			'vibe'        => array( 'Business', 'Learn' ),
			'date_label'  => 'AUG 24',
			'date_full'   => __( 'Aug 24, 2026', 'evently' ),
			'time'        => '09:00',
			'location'    => __( 'Dhaka, Bangladesh', 'evently' ),
			'venue'       => __( 'Pan Pacific Sonargaon', 'evently' ),
			'city'        => 'Dhaka',
			'country'     => 'Bangladesh',
			'price'       => 129,
			'price_label' => __( 'From $129', 'evently' ),
			'rating'      => 4.6,
			'image_url'   => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-2-future-business-summit.jpg',
			'organizer'   => __( 'Future Forum', 'evently' ),
			'date_type'   => 'fixed',
			'gallery_files' => array( 'demo-2-future-business-summit.jpg', 'category-conferences.jpg', 'demo-5-tech-innovation-conference.jpg', 'demo-8-startup-founders-meetup.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'Is this a networking-focused or lecture-focused event?', 'evently' ), 'a' => __( 'Both — the morning covers keynotes and panels, while the afternoon breakout sessions and lunch are built around structured networking.', 'evently' ) ),
				array( 'q' => __( 'Will sessions be recorded?', 'evently' ), 'a' => __( 'Yes, all main-stage sessions are recorded and shared with ticket holders within a week; breakout sessions are not recorded to keep discussions candid.', 'evently' ) ),
				array( 'q' => __( "What's the dress code?", 'evently' ), 'a' => __( "Business casual. Most attendees wear what they'd wear to a client meeting.", 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '09:00', 'title' => __( 'Registration & Morning Coffee', 'evently' ), 'desc' => __( 'Badge pickup and coffee in the main lobby.', 'evently' ) ),
				array( 'time' => '09:45', 'title' => __( 'Opening Keynote', 'evently' ), 'desc' => __( 'A direct look at where funding, hiring, and growth stand today.', 'evently' ) ),
				array( 'time' => '11:00', 'title' => __( 'Panel: Fundraising in 2026', 'evently' ), 'desc' => __( 'Investors and operators discuss how deals get done today, followed by open Q&A.', 'evently' ) ),
				array( 'time' => '12:30', 'title' => __( 'Working Lunch', 'evently' ), 'desc' => __( 'Assigned table topics on fundraising, go-to-market, hiring, and expansion.', 'evently' ) ),
				array( 'time' => '14:00', 'title' => __( 'Breakout Sessions', 'evently' ), 'desc' => __( 'Smaller-group discussions across three rooms.', 'evently' ) ),
				array( 'time' => '16:30', 'title' => __( 'Closing Fireside Chat', 'evently' ), 'desc' => __( 'A closing conversation followed by an informal networking hour with drinks.', 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-3',
			'start_date'  => '2026-10-10', // ISO date for import meta — date_full below is display-only ("(+2 more dates)" isn't machine-parseable).
			'title'       => __( 'Creative Design Workshop', 'evently' ),
			'excerpt'     => __( 'A hands-on day of brand, product and interaction design exercises.', 'evently' ),
			'description' => __( "Creative Design Workshop is a full, hands-on day built for anyone who wants to get better at brand, product, and interaction design by actually doing the work — not just watching slides. Held at Studio 9 in small cohorts capped at 24 people, the workshop trades lecture time for exercises, critique, and real feedback from working designers.\n\nThe day starts with a short framing session on where brand, product, and interaction design overlap, before moving straight into the first exercise: a brand-foundations sprint where small groups build a visual identity from a one-line brief in under ninety minutes. It's fast on purpose — the goal is to get comfortable making decisions under pressure, the same way real client work often demands.\n\nAfter lunch, the focus shifts to product design with a structured sprint covering a common flow — onboarding, checkout, or a settings screen, rotating by cohort — from wireframe to a clickable prototype. Facilitators circulate throughout, giving in-the-moment feedback rather than saving everything for the end. The final two hours are reserved for portfolio critique — bring a current project, live or in progress, and get direct, specific feedback from both facilitators and peers in a small-group format designed to avoid the usual awkward silence of group critiques.\n\nBecause this workshop runs periodically rather than as a single one-off, each date's cohort tends to have a slightly different mix — some sessions lean toward people switching into design from adjacent fields, others toward working designers sharpening a specific skill. Whichever date you book, the structure stays the same: a laptop with your usual design tool, a sketchbook, and a willingness to share unfinished work are the only prerequisites.\n\nEvery attendee leaves with a completed brand exercise, a working prototype, and written feedback on their portfolio piece — plus a certificate of completion. Seats are intentionally limited to keep the critique sessions genuinely useful, so early booking is recommended for whichever date works for your schedule.", 'evently' ),
			'category'    => __( 'Workshop', 'evently' ),
			'category_badge' => 'WORKSHOP',
			'vibe'        => array( 'Creative', 'Learn' ),
			'date_label'  => 'OCT 10',
			'date_full'   => __( 'Oct 10, 2026 (+2 more dates)', 'evently' ),
			'time'        => '10:00',
			'location'    => __( 'Dhaka, Bangladesh', 'evently' ),
			'venue'       => __( 'Studio 9', 'evently' ),
			'city'        => 'Dhaka',
			'country'     => 'Bangladesh',
			'price'       => 75,
			'price_label' => __( 'From $75', 'evently' ),
			'rating'      => 4.9,
			'image_url'   => 'https://images.unsplash.com/photo-1531058020387-3be344556be6?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-3-creative-design-workshop.jpg',
			'organizer'   => __( 'Studio Nine Collective', 'evently' ),
			'date_type'   => 'particular',
			'extra_dates' => array(
				array( 'date' => '2026-11-14', 'time' => '10:00', 'end_time' => '17:00' ),
				array( 'date' => '2026-12-12', 'time' => '10:00', 'end_time' => '17:00' ),
			),
			'gallery_files' => array( 'demo-3-creative-design-workshop.jpg', 'category-workshops.jpg', 'demo-7-photography-masterclass.jpg', 'journal-1-plan-unforgettable-event.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'Do I need design experience to attend?', 'evently' ), 'a' => __( 'No — the morning session is built for beginners and intermediate designers alike; bring a laptop with your usual design tool installed.', 'evently' ) ),
				array( 'q' => __( 'What should I bring?', 'evently' ), 'a' => __( "A laptop, sketchbook, and any current project you'd like feedback on during the portfolio critique.", 'evently' ) ),
				array( 'q' => __( 'Is there a certificate?', 'evently' ), 'a' => __( 'Yes, every attendee receives a certificate of completion at the end of the day.', 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '10:00', 'title' => __( 'Welcome & Materials', 'evently' ), 'desc' => __( 'Framing session on where brand, product, and interaction design overlap.', 'evently' ) ),
				array( 'time' => '10:30', 'title' => __( 'Brand Foundations Exercise', 'evently' ), 'desc' => __( 'Small groups build a visual identity from a one-line brief.', 'evently' ) ),
				array( 'time' => '12:30', 'title' => __( 'Lunch Break', 'evently' ), 'desc' => __( 'Lunch provided on site.', 'evently' ) ),
				array( 'time' => '13:30', 'title' => __( 'Product Design Sprint', 'evently' ), 'desc' => __( 'From wireframe to a clickable prototype, with facilitator feedback throughout.', 'evently' ) ),
				array( 'time' => '15:30', 'title' => __( 'Portfolio Critique', 'evently' ), 'desc' => __( 'Small-group critique of a current project you bring.', 'evently' ) ),
				array( 'time' => '17:00', 'title' => __( 'Wrap-up & Certificates', 'evently' ), 'desc' => __( 'Certificates of completion handed out.', 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-4',
			'start_date'  => '2026-10-03', // ISO date for import meta — date_full below is display-only (its en-dash range isn't reliably machine-parseable).
			'title'       => __( 'International Food Festival', 'evently' ),
			'excerpt'     => __( 'Street food, chef demos and tastings from over forty kitchens.', 'evently' ),
			'description' => __( "International Food Festival takes over the CRB Waterfront for two days of street food, chef demonstrations, and tastings from more than forty kitchens — a mix of long-running local favorites and visiting chefs representing cuisines from across the region and beyond.\n\nEvery ticket includes a tasting passport with five sample vouchers, redeemable at any participating stall, so the easiest way to start is simply walking the waterfront and picking whatever smells best. Beyond the vouchers, additional tastings run on a simple pay-as-you-go basis, with most sample plates priced to encourage trying several kitchens rather than filling up at one.\n\nThe programming runs alongside the eating: scheduled chef demonstrations happen roughly every two hours on the festival's demo stage, covering everything from street-food techniques to more technical regional specialties, and a live cooking competition midway through each day pits local kitchens against each other for audience votes. As afternoon turns to evening, the waterfront shifts character — a night market atmosphere takes over with live acoustic music, string lighting, and stalls staying open later than the daytime schedule.\n\nFamilies are genuinely welcome: a dedicated kids' zone runs both days with simpler food options and supervised activities, and children under five enter free. Every stall clearly labels vegetarian, vegan, and halal options, and a full allergen reference is posted at the festival's information tent for anyone with specific dietary needs.\n\nBecause the festival runs as one continuous two-day event rather than two separate dates, a single ticket covers both days — useful if you want to split your eating across two visits rather than trying every kitchen in one go. Waterfront seating is available but fills up during peak lunch and dinner hours, so grabbing a spot early or eating standing at the rail with a view of the river is a reasonable backup plan.", 'evently' ),
			'category'    => __( 'Food & Dining', 'evently' ),
			'category_badge' => 'FOOD',
			'vibe'        => array( 'Food', 'Family', 'Travel' ),
			'date_label'  => 'OCT 03',
			'date_full'   => __( 'Oct 3–4, 2026', 'evently' ),
			'time'        => '12:00',
			'location'    => __( 'Chittagong, Bangladesh', 'evently' ),
			'venue'       => __( 'CRB Waterfront', 'evently' ),
			'city'        => 'Chittagong',
			'country'     => 'Bangladesh',
			'price'       => 20,
			'price_label' => __( 'From $20', 'evently' ),
			'rating'      => 4.5,
			'image_url'   => 'https://images.unsplash.com/photo-1638132704795-6bb223151bf7?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-4-international-food-festival.jpg',
			'organizer'   => __( 'Taste of the World', 'evently' ),
			'date_type'   => 'fixed',
			'event_end_date_offset' => 1,
			'gallery_files' => array( 'demo-4-international-food-festival.jpg', 'category-food-dining.jpg', 'category-festivals.jpg', 'journal-3-future-of-live-events.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'Is the festival family-friendly?', 'evently' ), 'a' => __( "Yes, there's a dedicated kids' zone and several kid-friendly food stalls; children under 5 enter free.", 'evently' ) ),
				array( 'q' => __( 'How does the tasting passport work?', 'evently' ), 'a' => __( 'Your ticket includes a tasting passport with 5 sample vouchers redeemable at any participating kitchen; additional tastings are pay-as-you-go.', 'evently' ) ),
				array( 'q' => __( 'Are dietary restrictions accommodated?', 'evently' ), 'a' => __( "Vegetarian, vegan and halal options are clearly labeled at every stall, and a full allergen list is posted at the festival's information tent.", 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '12:00', 'title' => __( 'Gates Open & Tasting Passports', 'evently' ), 'desc' => __( 'Collect your tasting passport at the entrance.', 'evently' ) ),
				array( 'time' => '13:00', 'title' => __( 'Chef Demo: Street Food Classics', 'evently' ), 'desc' => __( 'Live demonstration on the festival demo stage.', 'evently' ) ),
				array( 'time' => '15:00', 'title' => __( 'Live Cooking Competition', 'evently' ), 'desc' => __( 'Local kitchens compete for audience votes.', 'evently' ) ),
				array( 'time' => '17:00', 'title' => __( 'Chef Demo: Regional Specialties', 'evently' ), 'desc' => __( 'A more technical demo focused on regional specialties.', 'evently' ) ),
				array( 'time' => '19:00', 'title' => __( 'Night Market & Live Music', 'evently' ), 'desc' => __( 'The waterfront shifts to a night-market atmosphere with live acoustic music.', 'evently' ) ),
				array( 'time' => '22:00', 'title' => __( 'Market Close', 'evently' ), 'desc' => __( 'Stalls close for the day.', 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-5',
			'start_date'  => '2026-09-21',
			'title'       => __( 'Tech Innovation Conference', 'evently' ),
			'excerpt'     => __( 'Two stages, six tracks — product, AI, infrastructure and design engineering.', 'evently' ),
			'description' => __( "Tech Innovation Conference runs across two stages and six tracks at ICCB, built for people who want a full day of genuinely technical content rather than another round of high-level buzzword keynotes. The day opens with registration and badge pickup at 9:30, followed by an opening keynote framing where the industry actually stands heading into next year — product, AI, infrastructure, and design engineering all get equal billing rather than AI dominating the whole agenda.\n\nFrom there, the day splits into two session rounds across six tracks, so there's a genuine choice at every slot rather than one obvious \"main\" track everyone funnels into. Past editions have run tracks on applied AI, platform infrastructure, design systems at scale, mobile engineering, developer experience, and early-stage product strategy — this year's exact track list is published on the event page once speakers are confirmed, typically a few weeks out.\n\nThe expo hall runs throughout the day rather than being confined to breaks, with more than forty startups and a handful of larger sponsors exhibiting live demos. It's deliberately positioned next to the lunch area, so the natural flow between sessions and lunch takes you straight past it rather than requiring a special detour.\n\nLunch itself is served buffet-style with enough seating to avoid the usual conference scramble, and the afternoon session round follows the same six-track structure before the day closes with a shared closing panel and a short awards segment recognizing standout startups from the expo hall.\n\nSessions are not livestreamed, but slides and, where speakers agree, session recordings are shared with all ticket holders within about a week. Group tickets are available for teams of five or more at a 15% discount, arranged in advance through the organizer. Whether you're there for one specific track or trying to get a broad read on where the industry is heading, Tech Innovation Conference is built around depth over spectacle.", 'evently' ),
			'category'    => __( 'Conference', 'evently' ),
			'category_badge' => 'CONFERENCE',
			'vibe'        => array( 'Business', 'Learn' ),
			'date_label'  => 'SEP 21',
			'date_full'   => __( 'Sep 21, 2026', 'evently' ),
			'time'        => '09:30',
			'location'    => __( 'Dhaka, Bangladesh', 'evently' ),
			'venue'       => __( 'ICCB', 'evently' ),
			'city'        => 'Dhaka',
			'country'     => 'Bangladesh',
			'price'       => 89,
			'price_label' => __( 'From $89', 'evently' ),
			'rating'      => 4.7,
			'image_url'   => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-5-tech-innovation-conference.jpg',
			'organizer'   => __( 'Evently Tech', 'evently' ),
			'date_type'   => 'fixed',
			'gallery_files' => array( 'demo-5-tech-innovation-conference.jpg', 'category-conferences.jpg', 'demo-2-future-business-summit.jpg', 'journal-3-future-of-live-events.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'How do I choose which track sessions to attend?', 'evently' ), 'a' => __( "Your ticket gets you into any track — there's no separate sign-up. The full session list with room assignments is in the event app, released a week before.", 'evently' ) ),
				array( 'q' => __( 'Is there a startup expo?', 'evently' ), 'a' => __( 'Yes, over 40 startups exhibit in the expo hall, open throughout the day between sessions.', 'evently' ) ),
				array( 'q' => __( 'Can I get a group discount?', 'evently' ), 'a' => __( 'Yes, groups of 5 or more get 15% off — contact the organizer through the event page to arrange group tickets.', 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '09:30', 'title' => __( 'Registration & Badge Pickup', 'evently' ), 'desc' => __( 'Badge pickup at the main entrance.', 'evently' ) ),
				array( 'time' => '10:00', 'title' => __( 'Opening Keynote: State of the Industry', 'evently' ), 'desc' => __( 'A look at product, AI, infrastructure, and design engineering heading into next year.', 'evently' ) ),
				array( 'time' => '11:15', 'title' => __( 'Track Sessions — Round 1', 'evently' ), 'desc' => __( 'Six parallel tracks across two stages.', 'evently' ) ),
				array( 'time' => '13:00', 'title' => __( 'Lunch & Expo Hall', 'evently' ), 'desc' => __( 'Buffet lunch alongside the startup expo hall.', 'evently' ) ),
				array( 'time' => '14:15', 'title' => __( 'Track Sessions — Round 2', 'evently' ), 'desc' => __( 'The afternoon round across the same six tracks.', 'evently' ) ),
				array( 'time' => '16:00', 'title' => __( 'Closing Panel & Awards', 'evently' ), 'desc' => __( 'Shared closing panel and startup awards segment.', 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-6',
			'start_date'  => '2026-11-02',
			'title'       => __( 'City Marathon', 'evently' ),
			'excerpt'     => __( '5K, 10K and full marathon routes through the historic city center.', 'evently' ),
			'description' => __( "City Marathon sends runners through the historic heart of the city on three distances — 5K, 10K, and the full marathon — all starting and finishing at Hatirjheel, with fully closed roads for the entire route length rather than the partial closures some city races settle for.\n\nRace morning starts early by design, to beat both the heat and the city's daytime traffic: bib pickup and bag drop open at 5:00 AM, the full marathon starts at 6:00, the 10K follows at 6:30, and the 5K rounds things out at 7:00. Staggering the starts keeps the early stretch of the route from getting overcrowded and gives full-marathon runners clear road for the opening kilometers when pacing matters most.\n\nThe full marathon route loops through Hatirjheel and out through several of the city's older neighborhoods before returning along the water, with marshals and medical support stationed roughly every two kilometers and pacers running set target times throughout the field — useful whether your goal is a personal best or simply finishing comfortably inside the six-hour cutoff. The 10K and 5K routes share the opening and closing stretches with the marathon but cut the loop shorter, so shorter-distance runners still get the same waterfront scenery without the full distance.\n\nAid stations are spaced through all three routes with water, electrolyte drinks, and basic first aid, and the finish line area at Hatirjheel stays open well past the final expected finishers, with a proper medal ceremony and age-category awards held once results are confirmed. Bib pickup is also available the evening before race day at an announced expo location, which is the better option if you'd rather not deal with a 5:00 AM queue.\n\nWhether you're chasing a marathon PB, tackling your first 10K, or bringing the family for the 5K, City Marathon is built to feel like a genuine city-wide event — closed streets, real crowd support along the route, and a finish line worth the early alarm.", 'evently' ),
			'category'    => __( 'Sports', 'evently' ),
			'category_badge' => 'SPORTS',
			'vibe'        => array( 'Sports', 'Travel' ),
			'date_label'  => 'NOV 02',
			'date_full'   => __( 'Nov 2, 2026', 'evently' ),
			'time'        => '06:00',
			'location'    => __( 'Dhaka, Bangladesh', 'evently' ),
			'venue'       => __( 'Hatirjheel', 'evently' ),
			'city'        => 'Dhaka',
			'country'     => 'Bangladesh',
			'price'       => 30,
			'price_label' => __( 'From $30', 'evently' ),
			'rating'      => 4.4,
			'image_url'   => 'https://images.unsplash.com/photo-1705593973313-75de7bf95b56?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-6-city-marathon.jpg',
			'organizer'   => __( 'City Runners Club', 'evently' ),
			'date_type'   => 'fixed',
			'gallery_files' => array( 'demo-6-city-marathon.jpg', 'category-sports.jpg', 'category-festivals.jpg', 'journal-1-plan-unforgettable-event.jpg' ),
			'faq'         => array(
				array( 'q' => __( "What's the cutoff time for the full marathon?", 'evently' ), 'a' => __( '6 hours from the starting gun; pacers are stationed along the route to help you track your target time.', 'evently' ) ),
				array( 'q' => __( 'Where do I pick up my bib?', 'evently' ), 'a' => __( 'Bib pickup is at Hatirjheel from 05:00 on race day, or the evening before at the announced expo location.', 'evently' ) ),
				array( 'q' => __( 'Are the routes closed to traffic?', 'evently' ), 'a' => __( 'Yes, all three routes run on fully closed roads with marshals and medical support stationed every 2km.', 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '05:00', 'title' => __( 'Bib Pickup & Bag Drop', 'evently' ), 'desc' => __( 'Collect your race bib and drop your bag at the baggage tent.', 'evently' ) ),
				array( 'time' => '06:00', 'title' => __( 'Full Marathon Start', 'evently' ), 'desc' => __( 'The full marathon field starts from Hatirjheel.', 'evently' ) ),
				array( 'time' => '06:30', 'title' => __( '10K Start', 'evently' ), 'desc' => __( 'The 10K field starts.', 'evently' ) ),
				array( 'time' => '07:00', 'title' => __( '5K Start', 'evently' ), 'desc' => __( 'The 5K field starts.', 'evently' ) ),
				array( 'time' => '09:30', 'title' => __( 'First Finishers Expected', 'evently' ), 'desc' => __( 'Leading full-marathon runners expected to finish.', 'evently' ) ),
				array( 'time' => '11:00', 'title' => __( 'Medal Ceremony & Awards', 'evently' ), 'desc' => __( 'Medal ceremony and age-category awards at the finish line.', 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-7',
			'start_date'  => '2026-10-18', // ISO date for import meta — date_full below is display-only ("Weekly from ..." isn't machine-parseable).
			'title'       => __( 'Photography Masterclass', 'evently' ),
			'excerpt'     => __( 'A full-day masterclass on composition, light and post-production.', 'evently' ),
			'description' => __( "Photography Masterclass is a full-day, hands-on class covering composition, light, and post-production — run in small groups at the Sylhet Arts Centre and repeated on a regular schedule so you can pick whichever date fits your calendar rather than waiting for a single annual sitting.\n\nThe morning is built around composition fundamentals: framing, leading lines, and how to read a scene before raising the camera, taught through short demonstrations followed immediately by practice rather than long lecture blocks. Whatever camera you shoot with — a mirrorless body, a DSLR, or just your phone — the material is built around seeing, not gear, so no specific equipment is required to keep up.\n\nAfter lunch, the class moves outdoors for a golden-hour field session timed to the day's actual light, applying the morning's composition ideas to real, changing conditions instead of a controlled studio setup. This is consistently the part past attendees mention first — shooting with immediate feedback from an instructor standing next to you catches habits that are much harder to correct from photos reviewed days later.\n\nThe day closes with a post-production lab using free editing tools, so there's no paid software subscription required to follow along, followed by a group review where everyone's best shot from the day gets discussed openly. It's a deliberately public critique format, and most attendees find it's the fastest way to absorb feedback.\n\nBecause the class runs on a repeating schedule, each date's small group tends to bring a different mix of experience levels, and the material adjusts slightly session to session — a class full of beginners spends more time on fundamentals, while a more experienced group moves faster into the field session. Returning students get a 20% loyalty discount on any later date, which a fair number end up using once they see how much a second field session under different light adds. Bring a camera you're comfortable with, comfortable shoes for the outdoor session, and a willingness to have your photos critiqued in front of the group.", 'evently' ),
			'category'    => __( 'Workshop', 'evently' ),
			'category_badge' => 'WORKSHOP',
			'vibe'        => array( 'Creative', 'Learn' ),
			'date_label'  => 'OCT 18',
			'date_full'   => __( 'Weekly from Oct 18, 2026', 'evently' ),
			'time'        => '10:00',
			'location'    => __( 'Sylhet, Bangladesh', 'evently' ),
			'venue'       => __( 'Sylhet Arts Centre', 'evently' ),
			'city'        => 'Sylhet',
			'country'     => 'Bangladesh',
			'price'       => 65,
			'price_label' => __( 'From $65', 'evently' ),
			'rating'      => 4.9,
			'image_url'   => 'https://images.unsplash.com/photo-1512540452972-baac55d40ef1?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-7-photography-masterclass.jpg',
			'organizer'   => __( 'Frame & Light', 'evently' ),
			'date_type'   => 'recurring',
			'recurrence'  => array( 'period' => 'weekly', 'end_date' => '2026-11-08' ),
			'gallery_files' => array( 'demo-7-photography-masterclass.jpg', 'category-workshops.jpg', 'demo-3-creative-design-workshop.jpg', 'journal-2-festival-tickets.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'What gear do I need?', 'evently' ), 'a' => __( "Any camera you're comfortable with, including a smartphone — this class is about composition and light, not gear.", 'evently' ) ),
				array( 'q' => __( 'Is editing software included?', 'evently' ), 'a' => __( "We'll use free tools during the post-production lab, so no paid software subscription is required to follow along.", 'evently' ) ),
				array( 'q' => __( 'Can I attend more than one session?', 'evently' ), 'a' => __( 'Yes, each date covers different material, and returning students get a 20% loyalty discount on their second booking.', 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '10:00', 'title' => __( 'Welcome & Gear Check', 'evently' ), 'desc' => __( 'Quick intros and a look at what everyone brought.', 'evently' ) ),
				array( 'time' => '10:30', 'title' => __( 'Composition Fundamentals', 'evently' ), 'desc' => __( 'Framing, leading lines, and reading a scene, taught through demo-then-practice.', 'evently' ) ),
				array( 'time' => '12:30', 'title' => __( 'Lunch Break', 'evently' ), 'desc' => __( 'Lunch provided on site.', 'evently' ) ),
				array( 'time' => '13:30', 'title' => __( 'Golden Hour Field Session', 'evently' ), 'desc' => __( 'Outdoor shooting session with live instructor feedback.', 'evently' ) ),
				array( 'time' => '16:00', 'title' => __( 'Post-Production Lab', 'evently' ), 'desc' => __( 'Editing the day\'s shots using free tools.', 'evently' ) ),
				array( 'time' => '17:30', 'title' => __( 'Group Review & Feedback', 'evently' ), 'desc' => __( "Open review of everyone's best shot from the day.", 'evently' ) ),
			),
		),
		array(
			'id'          => 'demo-8',
			'start_date'  => '2026-09-14', // ISO date for import meta — date_full below is display-only ("Monthly from ..." isn't machine-parseable).
			'title'       => __( 'Startup Founders Meetup', 'evently' ),
			'excerpt'     => __( 'An informal evening of lightning pitches, networking and community.', 'evently' ),
			'description' => __( "Startup Founders Meetup is a free, informal evening at Startup Bangladesh Hub built around lightning pitches, real networking, and a community that keeps coming back month after month rather than a one-off networking mixer.\n\nDoors open at 6:00 PM with food and drinks provided, and the first half hour is deliberately unstructured — arrive, get a drink, and start talking before any formal programming begins. A short welcome and community updates segment follows at 6:30, covering anything relevant to the founder community locally: upcoming events, funding news, or a quick shoutout to a member who hit a milestone since the last meetup.\n\nThe centerpiece of each evening is the lightning pitch segment: five founders get five minutes each to pitch whatever they're working on, followed by open questions from the room rather than a panel of judges. It's kept casual on purpose — this isn't a pitch competition with a winner, it's a chance to get real reactions from other founders and operators who've likely faced the same problem you're describing. Pitch slots are limited and go through a simple sign-up link sent after registration.\n\nAfter the pitches, the rest of the evening is open networking until doors close at 8:30 — no assigned tables, no forced icebreakers, just a room full of people building things who are generally happy to talk shop. Because the meetup runs monthly rather than as a single event, the community has grown into something closer to a recurring hangout than a typical networking event: regulars know each other, newcomers get folded in quickly, and conversations from one month often pick back up at the next.\n\nAll sectors are welcome — the room includes tech founders, but just as often people building retail, food, and creative businesses. There's no cost to attend and no dress code beyond what you'd wear to work; just show up ready to talk about what you're building.", 'evently' ),
			'category'    => __( 'Business', 'evently' ),
			'category_badge' => 'BUSINESS',
			'vibe'        => array( 'Business' ),
			'date_label'  => 'SEP 14',
			'date_full'   => __( 'Monthly from Sep 14, 2026', 'evently' ),
			'time'        => '18:00',
			'location'    => __( 'Dhaka, Bangladesh', 'evently' ),
			'venue'       => __( 'Startup Bangladesh Hub', 'evently' ),
			'city'        => 'Dhaka',
			'country'     => 'Bangladesh',
			'price'       => 0,
			'price_label' => __( 'Free', 'evently' ),
			'rating'      => 4.7,
			'image_url'   => 'https://images.unsplash.com/photo-1565035010268-a3816f98589a?w=900&h=680&fit=crop&auto=format',
			'image_file'   => 'demo-8-startup-founders-meetup.jpg',
			'organizer'   => __( 'Founders Bangladesh', 'evently' ),
			'date_type'   => 'recurring',
			'recurrence'  => array( 'period' => 'monthly', 'end_date' => '2026-12-14' ),
			'gallery_files' => array( 'demo-8-startup-founders-meetup.jpg', 'category-conferences.jpg', 'demo-2-future-business-summit.jpg', 'journal-1-plan-unforgettable-event.jpg' ),
			'faq'         => array(
				array( 'q' => __( 'Is this meetup free?', 'evently' ), 'a' => __( 'Yes, entry is free for all founders and aspiring founders; food and drinks are provided.', 'evently' ) ),
				array( 'q' => __( 'Can I pitch on stage?', 'evently' ), 'a' => __( 'Lightning pitch slots are limited to 5 founders per meetup — sign up via the link in your confirmation email after registering.', 'evently' ) ),
				array( 'q' => __( 'Is this only for tech startups?', 'evently' ), 'a' => __( 'No, all sectors are welcome; the community includes founders from tech, retail, food, and creative businesses.', 'evently' ) ),
			),
			'timeline'    => array(
				array( 'time' => '18:00', 'title' => __( 'Doors Open & Networking', 'evently' ), 'desc' => __( 'Food and drinks provided; unstructured mingling.', 'evently' ) ),
				array( 'time' => '18:30', 'title' => __( 'Welcome & Community Updates', 'evently' ), 'desc' => __( 'Quick round-up of local founder community news.', 'evently' ) ),
				array( 'time' => '18:45', 'title' => __( 'Lightning Pitches', 'evently' ), 'desc' => __( 'Five founders pitch for five minutes each, followed by open questions.', 'evently' ) ),
				array( 'time' => '19:30', 'title' => __( 'Open Networking', 'evently' ), 'desc' => __( 'Unstructured networking continues.', 'evently' ) ),
				array( 'time' => '20:30', 'title' => __( 'Doors Close', 'evently' ), 'desc' => __( "That evening's meetup wraps up.", 'evently' ) ),
			),
		),
	);

	return $events;
}

/**
 * Resolve the display image for a demo event/article: prefer the bundled
 * local file under assets/images/demo/ (real, licensed-for-commercial-use
 * photography shipped with the theme — no runtime dependency on a
 * third-party host) and only fall back to the original hotlinked Unsplash
 * URL if the bundled file is somehow missing.
 *
 * @param array $demo_item An entry from evently_demo_events() or evently_demo_journal_articles().
 * @return string
 */
function evently_demo_image_url( $demo_item ) {
	if ( ! empty( $demo_item['image_file'] ) ) {
		$path = EVENTLY_DIR . 'assets/images/demo/' . $demo_item['image_file'];
		if ( is_readable( $path ) ) {
			return EVENTLY_URI . 'assets/images/demo/' . $demo_item['image_file'];
		}
	}

	return isset( $demo_item['image_url'] ) ? $demo_item['image_url'] : ( isset( $demo_item['image'] ) ? $demo_item['image'] : '' );
}

/**
 * Resolve the bundled local URLs for a demo event's gallery (brief's new
 * "every event should have a Gallery" requirement). Skips any listed file
 * that isn't actually readable on disk instead of producing a broken image.
 *
 * @param array $demo_event An entry from evently_demo_events().
 * @return string[] Absolute URLs, in the order listed by the event's gallery_files.
 */
function evently_demo_gallery_urls( $demo_event ) {
	$urls = array();

	if ( empty( $demo_event['gallery_files'] ) || ! is_array( $demo_event['gallery_files'] ) ) {
		return $urls;
	}

	foreach ( $demo_event['gallery_files'] as $file ) {
		$path = EVENTLY_DIR . 'assets/images/demo/' . $file;
		if ( is_readable( $path ) ) {
			$urls[] = EVENTLY_URI . 'assets/images/demo/' . $file;
		}
	}

	return $urls;
}

/**
 * Map an entry from evently_demo_events() onto the shape
 * evently_normalize_event()/evently_event_card() expects.
 *
 * @param array $demo_event One entry from evently_demo_events().
 * @return array
 */
function evently_demo_event_to_card( $demo_event ) {
	return array(
		'id'           => $demo_event['id'],
		'title'        => $demo_event['title'],
		'url'          => evently_get_events_page_url(),
		'image_url'    => evently_demo_image_url( $demo_event ),
		'image_alt'    => $demo_event['title'],
		'date_label'   => $demo_event['date_label'],
		'date_full'    => $demo_event['date_full'],
		'time'         => $demo_event['time'],
		'location'     => $demo_event['location'],
		'category'     => $demo_event['category_badge'],
		'price_label'  => $demo_event['price_label'],
		'price'        => $demo_event['price'],
		'rating'       => $demo_event['rating'],
		'organizer'    => $demo_event['organizer'],
		'availability' => '',
		'is_favorite'  => false,
	);
}

/**
 * Homepage category tiles (brief §11 Categories section).
 *
 * @return array[]
 */
function evently_demo_categories() {
	return array(
		array(
			'label'      => __( 'Concerts', 'evently' ),
			'image'      => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=800&h=500&fit=crop&auto=format',
			'image_file' => 'category-concerts.jpg',
			'wide'       => true,
		),
		array(
			'label'      => __( 'Conferences', 'evently' ),
			'image'      => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=600&h=500&fit=crop&auto=format',
			'image_file' => 'category-conferences.jpg',
			'wide'       => false,
		),
		array(
			'label'      => __( 'Sports', 'evently' ),
			'image'      => 'https://images.unsplash.com/photo-1563299796-b729d0af54a5?w=600&h=500&fit=crop&auto=format',
			'image_file' => 'category-sports.jpg',
			'wide'       => false,
		),
		array(
			'label'      => __( 'Festivals', 'evently' ),
			'image'      => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=600&h=500&fit=crop&auto=format',
			'image_file' => 'category-festivals.jpg',
			'wide'       => false,
		),
		array(
			'label'      => __( 'Food & Dining', 'evently' ),
			'image'      => 'https://images.unsplash.com/photo-1638132704795-6bb223151bf7?w=600&h=500&fit=crop&auto=format',
			'image_file' => 'category-food-dining.jpg',
			'wide'       => false,
		),
	);
}

/**
 * A light, homepage-calendar-only dataset for August 2026 (brief §11 Event
 * Calendar section). Intentionally includes a couple of entries that are
 * NOT in the main 8 trending events — the calendar is meant to feel like a
 * broader monthly view, not a re-list of the trending grid.
 *
 * @return array<int, array[]> Map of day-of-month => list of {title, location, price}.
 */
function evently_demo_calendar_events() {
	return array(
		5  => array( array( 'title' => __( 'Startup Networking Night', 'evently' ), 'location' => 'Dhaka', 'price' => __( 'Free', 'evently' ) ) ),
		14 => array( array( 'title' => __( 'Indie Folk Showcase', 'evently' ), 'location' => 'Dhaka', 'price' => __( 'From $25', 'evently' ) ) ),
		24 => array(
			array( 'title' => __( 'Summer Music Festival', 'evently' ), 'location' => 'Dhaka', 'price' => __( 'From $49', 'evently' ) ),
			array( 'title' => __( 'Future Business Summit', 'evently' ), 'location' => 'Dhaka', 'price' => __( 'From $129', 'evently' ) ),
		),
		28 => array( array( 'title' => __( 'Food & Culture Fair', 'evently' ), 'location' => 'Dhaka', 'price' => __( 'From $18', 'evently' ) ) ),
	);
}

/**
 * Homepage statistics strip (brief §11 Statistics).
 *
 * @return array[]
 */
function evently_demo_stats() {
	return array(
		array( 'value' => '10K+', 'label' => __( 'Events', 'evently' ) ),
		array( 'value' => '250K+', 'label' => __( 'Tickets Sold', 'evently' ) ),
		array( 'value' => '98%', 'label' => __( 'Customer Satisfaction', 'evently' ) ),
		array( 'value' => '50+', 'label' => __( 'Cities', 'evently' ) ),
	);
}

/**
 * Homepage testimonials (brief §11 Testimonials). Clearly fictional demo
 * content, standard practice for theme preview content.
 *
 * @return array[]
 */
function evently_demo_testimonials() {
	return array(
		array(
			'stars'    => 5,
			'text'     => __( 'Everything from discovering the event to receiving my ticket felt effortless. This is how event booking should work.', 'evently' ),
			'name'     => 'Sarah Williams',
			'role'     => __( 'Event Organizer', 'evently' ),
			'initials' => 'SW',
			'color'    => '#6C5CE7',
		),
		array(
			'stars'    => 5,
			'text'     => __( "I've tried every event app out there. Evently is the only one that actually feels premium. The ticket experience is beautiful.", 'evently' ),
			'name'     => 'Marcus Chen',
			'role'     => __( 'Festival Goer', 'evently' ),
			'initials' => 'MC',
			'color'    => '#FF7657',
		),
		array(
			'stars'    => 5,
			'text'     => __( 'Sold out our 500-person conference in 48 hours. The dashboard made it simple to track everything in real time.', 'evently' ),
			'name'     => 'Priya Nair',
			'role'     => __( 'Conference Director', 'evently' ),
			'initials' => 'PN',
			'color'    => '#16A34A',
		),
	);
}

/**
 * Organizer dashboard demo stats (brief §11 Organizer CTA + §23). This is
 * illustrative UI content only — never presented as a live data feed, see
 * template-parts/home/organizer-cta.php.
 *
 * @return array[]
 */
function evently_demo_dashboard_stats() {
	return array(
		array( 'label' => __( 'Revenue', 'evently' ), 'value' => '$48,290', 'change' => '+12.4%' ),
		array( 'label' => __( 'Tickets Sold', 'evently' ), 'value' => '2,840', 'change' => '+8.1%' ),
		array( 'label' => __( 'Upcoming Events', 'evently' ), 'value' => '24', 'change' => '→' ),
		array( 'label' => __( 'Conversion', 'evently' ), 'value' => '8.42%', 'change' => '+0.3%' ),
	);
}

/**
 * Event Journal (blog) demo articles (brief §11/§24 — exact titles specified).
 *
 * @return array[]
 */
function evently_demo_journal_articles() {
	return array(
		array(
			'title'      => __( 'How to plan an unforgettable event', 'evently' ),
			'category'   => __( 'Events', 'evently' ),
			'date'       => 'Aug 2, 2026',
			'image'      => 'https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?w=700&h=460&fit=crop&auto=format',
			'image_file' => 'journal-1-plan-unforgettable-event.jpg',
		),
		array(
			'title'      => __( '5 things to know before buying festival tickets', 'evently' ),
			'category'   => __( 'Festivals', 'evently' ),
			'date'       => 'Jul 28, 2026',
			'image'      => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=700&h=460&fit=crop&auto=format',
			'image_file' => 'journal-2-festival-tickets.jpg',
		),
		array(
			'title'      => __( 'The future of live events', 'evently' ),
			'category'   => __( 'Culture', 'evently' ),
			'date'       => 'Jul 15, 2026',
			'image'      => 'https://images.unsplash.com/photo-1569783721854-33a99b4c0bae?w=700&h=460&fit=crop&auto=format',
			'image_file' => 'journal-3-future-of-live-events.jpg',
		),
	);
}
