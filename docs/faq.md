# FAQ

**Do I need the Event Booking plugin?**
For the booking/ticketing features, yes — Evently is presentation-only and has no booking logic of its own (by design, see `docs/booking-integration.md`). Without it, the theme still runs: the homepage/archive/blog show gracefully with demo-style fallback content, and event-specific pages show an honest "requires the Evently Booking plugin" notice instead of a broken page.

**Do I need WooCommerce?**
Only for ticket checkout/payment. The booking plugin can also run in a native/offline checkout mode without WooCommerce — see `docs/woocommerce.md`.

**Can I use a different event-booking plugin?**
Yes, but it requires development work: rewrite `Evently_Booking_Adapter`'s methods to call your plugin's real API. See `docs/booking-integration.md` → "Swapping in a different booking plugin."

**Is the Organizer Dashboard real, or a mockup?**
Real. It shows actual revenue/ticket totals (computed from real order/attendee records) for whichever events the logged-in user authored. It is not a multi-tenant SaaS product — "organizer" here means "the WordPress user who created the event post," which is the only organizer concept the underlying plugin's data model supports.

**Does the Digital Ticket / QR code actually work?**
The homepage's ticket showcase is a marketing visual — the QR pattern is decorative, not a real scannable code (no QR feature exists in the inspected plugin; it's a separate paid add-on). The real My Tickets / booking-details experience lives in My Account, styled from the plugin's real data.

**Is the theme translation-ready?**
Yes — every user-facing string uses `evently` as its text domain; `languages/evently.pot` is included and current.

**Is RTL supported?**
Partially. `language_attributes()`/`dir` output is automatic, and no layout uses hardcoded left/right positioning that would look badly broken mirrored, but a full logical-properties (`margin-inline-start` etc.) pass across every stylesheet hasn't been completed — treat RTL as "usable, not pixel-perfect" today. This is the one item on `docs/implementation-plan.md`'s scope that's explicitly deferred rather than shipped half-done and undocumented.
