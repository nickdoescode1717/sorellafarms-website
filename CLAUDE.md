# Sorella 1881 Website

Static marketing website for **Sorella Farms / Sorella 1881**, a wedding venue in St. Catharines, Ontario (Niagara Wine Country). Hand-authored HTML — no build step, no framework, no package manager.

## Stack & Hosting
- **Plain static HTML** styled with **Tailwind via CDN (3.4.17)** plus a per-page inline `<style>` block. No compilation — edit the `.html` files directly and they're live once uploaded.
- Icons: **Phosphor Icons** (CDN).
- Fonts: **Playfair Display** (serif headings) + **Inter** (body).
- **Hosted on cPanel / SiteGround**, in `public_html`. Deploy = upload changed files via cPanel File Manager (it replaced the old WordPress site; old WP files are archived in `Old Sorella/`).
- Git remote: `github.com/nickdoescode1717/sorellafarms-website`. Commit/push only when the user asks.

## Pages
- `index.html` — Homepage (local video hero)
- `weddings.html` — Wedding experience
- `venue.html` — Estate spaces + Ranch House accommodations
- `packages.html` — Package tiers / what's included
- `gallery.html` — Masonry photo grid with filter tabs + lightbox
- `about.html` — Family story + La Dolce Sera + team
- `contact.html` — Inquiry form + contact info

## Inquiry Form (important)
- `contact.html` posts via AJAX to **`send-inquiry.php`** (native PHP `mail()`). **Not Web3Forms** — that was removed.
- The PHP replicates the old WordPress Contact Form 7 flow: sends (1) an internal notification to `hayley@sorellafarms.ca`, and (2) an **autoresponder** ("Re: Online Inquiry", from Hayley) back to the person who inquired. The auto-reply HTML was recovered verbatim from the old CF7 config in `Old Sorella/sorellafarms_site.sql`.
- Form field `name`s are hyphenated (`your-name`, `partner-name`, `email`, `phone`, `wedding-date`, `guest-count`, `how-heard`, `message`) plus a hidden `botcheck` honeypot. The PHP reads these keys exactly — **keep field `name`s and PHP keys in sync** if you change the form.
- The JS expects a JSON reply of `{"success": true}`; keep that contract if editing `send-inquiry.php`.
- Deliverability relies on server `mail()` (works because it sends from the same host). If auto-replies start hitting spam, the fix is authenticated SMTP (not yet implemented).

## Design System
- Background cream `#FDFBF7` · accent/gold `#9A8467` · dark text `#2C2C2C` · light section `#F4EEE8` · footer dark `#2C2926`
- Maintain this palette and the fonts above on any new work.

## Photos
Stored under `Photos Folder/stitch_sorella_1881_design_brief_plan/`. In HTML the space is URL-encoded as `Photos%20Folder/...`.

## Key Facts
- Location: St. Catharines, Ontario · Capacity: up to ~230–240 guests
- Spaces: Blanc Marquee, Courtyard, The Pond, Cocktail Terrace, 1881 Barn, Old Maple Meadow, Pear Orchard, Vineyard
- Accommodations: The Ranch House (sleeps 12) · Dining: La Dolce Sera (in-house farm-to-table)
- Contact: hayley@sorellafarms.ca · Instagram @sorella1881 · by appointment only

## Notes
- `Old Sorella/` = archive of the previous WordPress site (core files, `wp-content/plugins`, and a full DB export). Reference only — not part of the live site. Don't deploy it.
