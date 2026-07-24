<?php
/**
 * Sorella 1881 — Inquiry form handler
 *
 * Replicates the old Contact Form 7 behaviour that used to run on the
 * WordPress site. On submission it sends TWO emails:
 *   1. An internal notification to the venue (hayley@sorellafarms.ca).
 *   2. An automated reply back to the person who inquired, from Hayley.
 *
 * The form (contact.html) posts here via AJAX and expects a JSON response
 * of the shape {"success": true} or {"success": false, "message": "..."}.
 */

header('Content-Type: application/json; charset=utf-8');

// ---- Only accept POST -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ---- Config -----------------------------------------------------------------
$VENUE_EMAIL   = 'hayley@sorellafarms.ca';   // where inquiries are received
$FROM_INTERNAL = 'info@sorellafarms.ca';     // "from" for the internal notice
$FROM_AUTO     = 'hayley@sorellafarms.ca';   // "from" for the auto-reply
$SITE_TITLE    = 'Sorella 1881';

// ---- Helpers ----------------------------------------------------------------
/** Collapse a posted value to a clean single line (also strips header-injection chars). */
function field($key) {
    $val = isset($_POST[$key]) ? $_POST[$key] : '';
    if (is_array($val)) { $val = implode(', ', $val); }
    return trim($val);
}
/** Remove CR/LF so a value can be safely placed in an email header. */
function header_safe($val) {
    return trim(str_replace(["\r", "\n", "%0a", "%0d"], '', $val));
}

// ---- Honeypot: silently succeed if a bot filled the hidden field ------------
if (field('botcheck') !== '') {
    echo json_encode(['success' => true]);
    exit;
}

// ---- Read fields ------------------------------------------------------------
$name    = field('your-name');
$partner = field('partner-name');
$email   = field('email');
$phone   = field('phone');
$date    = field('wedding-date');
$guests  = field('guest-count');
$heard   = field('how-heard');
$message = field('message');

// ---- Validate ---------------------------------------------------------------
if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please provide your name and a valid email address.']);
    exit;
}

$name_safe  = header_safe($name);
$email_safe = header_safe($email);

// =============================================================================
// EMAIL 1 — Internal notification to the venue (was CF7 "Mail" tab)
// =============================================================================
$notify_subject = 'Online Inquiry';
$notify_body =
    "Subject: Online Inquiry\n\n" .
    "From: {$name}\n" .
    ($partner !== '' ? "Partner's Name: {$partner}\n" : '') .
    "Cell Phone: {$phone}\n" .
    "Email: {$email}\n\n" .
    "Type of Event: Wedding\n" .
    "Date: {$date}\n" .
    "Number of Guests: {$guests}\n" .
    ($heard !== '' ? "How They Heard About Us: {$heard}\n" : '') .
    "\nMessage Body:\n{$message}\n\n" .
    "-- \nThis e-mail was sent from the inquiry form on {$SITE_TITLE} (https://www.sorellafarms.ca)";

$notify_headers  = "From: {$SITE_TITLE} <{$FROM_INTERNAL}>\r\n";
$notify_headers .= "Reply-To: {$name_safe} <{$email_safe}>\r\n";
$notify_headers .= "MIME-Version: 1.0\r\n";
$notify_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent_notify = mail($VENUE_EMAIL, $notify_subject, $notify_body, $notify_headers, "-f{$FROM_INTERNAL}");

// =============================================================================
// EMAIL 2 — Automated reply to the inquirer (was CF7 "Mail (2)" tab)
// Body is reproduced exactly from the old Contact Form 7 autoresponder.
// =============================================================================
$auto_subject = 'Re: Online Inquiry';
$auto_to      = "\"{$name_safe}\" <{$email_safe}>";

$auto_body = <<<HTML
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<div dir="ltr" style="font-family: Aptos, Aptos_MSFontService, -apple-system, Roboto, Arial, Helvetica, sans-serif; font-size: 12pt;">
  <p>Hi there,&nbsp; </p>
  <p>Yayyy!!! You're engaged! &#128141; </p>
  <p>This is your time! Picking a venue is where the magic begins, and we're so excited that Sorella 1881 could be the backdrop to your story.&nbsp; </p>
  <p>Why settle for a wedding that lasts just a few hours when you could celebrate for an entire weekend? Why not get married at an Airbnb and truly enjoy the property with your closest people? That's exactly what we did at Sorella. </p>
  <p>My name is Hayley, and I'm the owner of Sorella. We've been a working farm since 1881 and have been hosting weddings since 2023. I got married at the property in July 2025, and I can genuinely say it was everything I dreamed of and more. The estate gave us goosebumps when the sun went down- <a href="https://drive.google.com/file/d/1deYcqqyrVezCxjUKseRCjWDDMOnuVDcu/view?usp=sharing">take a peek here </a>.&nbsp;The freedom to personalize every detail, combined with the joy of gathering our closest people in one place, made it feel like we were celebrating at a villa in the European countryside. I'm so excited to now share that same possibility with you!&nbsp;&nbsp; </p>
  <p>To help you get a feel for the estate, we invite you to explore our <a href="https://drive.google.com/file/d/1YPpRoUJ1VgsDMw5F7S2GJmx3Oce2PnqQ/view?usp=sharing">Photo Magazine </a>&nbsp;and take a look through our linked <a href="https://drive.google.com/file/d/1A1LkgNS6zl6-Il7N2sqR5vZxrwoqcRbS/view?usp=sharing">Wedding Weekend Package </a>&nbsp;for pricing and details. </p>
  <p>Ready to see it in person? We'd love to host you for a <a href="https://calendly.com/hayley-sorellafarms/30min">Private Tour </a>. It's the best way to experience the beauty of the grounds and imagine what your celebration could look like. </p>
  <p>At Sorella 1881, we offer a full estate package with private access, including ceremony and reception spaces, luxury accommodations for 12 guests, a draped and lit marquee for up to 230 people, and the surrounding grounds. This model gives you total flexibility.. bring in your dream vendors or use ours, set your own timeline, and design a weekend that's completely yours. Oh, and we go until 1am! </p>
  <p>Whether you're envisioning an alfresco dinner under the stars, a courtyard ceremony, or a weekend-long gathering with your favorite people, this approach gives you the space and freedom to make it all happen. It's like hosting your own destination wedding in Niagara wine country. </p>
  <p>As a family-owned, hospitality-driven venue, we take great pride in custom tailoring every celebration. </p>
  <p>Warmest congratulations,</p>
</div>
<br>
<div dir="ltr" style="font-family: serif; font-size: 11pt; color: black;">
<b>Hayley Schwenker&nbsp;</b>
<br>Owner | Sorella Farms ~ Est. 1881
<br>Call/Text: <span style="font-family: serif; font-size: 11pt; color: rgb(0, 120, 212);">905-980-4855</span>
<br><i>Social Media: <a href="https://www.instagram.com/sorella1881" rel="noreferrer noopener" style="margin-top: 0px; margin-bottom: 0px;">@sorella1881</a></i>
<br><i>AirBnB: <a href="https://abnb.me/dZ1LQaIBOGb" rel="noreferrer noopener" style="margin-top: 0px; margin-bottom: 0px;">https://abnb.me/dZ1LQaIBOGb</a></i>
</div>
<hr style="display:inline-block;width:98%" tabindex="-1">
</body>
HTML;

$auto_headers  = "From: {$SITE_TITLE} <{$FROM_AUTO}>\r\n";
$auto_headers .= "Reply-To: {$FROM_AUTO}\r\n";
$auto_headers .= "MIME-Version: 1.0\r\n";
$auto_headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$sent_auto = mail($auto_to, $auto_subject, $auto_body, $auto_headers, "-f{$FROM_AUTO}");

// ---- Respond ----------------------------------------------------------------
// The inquiry is considered received as long as the venue got the notification.
if ($sent_notify) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Your inquiry could not be sent. Please email hayley@sorellafarms.ca directly.']);
}
