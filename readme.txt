=== Hushot Landing Page Builder ===
Contributors: hushot
Tags: landing page, lead generation, whatsapp, saas, pwa
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.8.9
License: GPLv2 or later

Professional landing page builder for African businesses.

== Description ==
Create beautiful landing pages with WhatsApp integration, lead forms, and Flutterwave payments.

== Changelog ==
= 1.8.9 =
* CRITICAL FIX: Visual Builder JSON - Fixed blank published pages caused by JSON corruption
* CRITICAL FIX: Seller Dashboard/Payout pages now load inside dashboard shell (no website header/footer)
* CRITICAL FIX: Seller Payout form now properly responds to user actions
* FIXED: Improved JSON decoding with fallbacks for corrupted section data
* FIXED: Added seller-dashboard and seller-setup to force_system_pages routing
* FIXED: Bank/Mobile Money toggle now works correctly with proper event listeners
* FIXED: Payout settings now save and persist to user meta reliably
* NEW: Mobile Money payout option (MTN, Vodafone, AirtelTigo, M-Pesa)
* NEW: Ability to update payout settings after initial setup
* NEW: Visual feedback during account verification
* IMPROVED: Seller Setup UI with cleaner field toggling
* IMPROVED: Landing page template error handling for invalid JSON

= 1.8.4 =
* CRITICAL: URL Migration - All old hushot-* URLs redirect to clean URLs (/dashboard, /checkout, etc.)
* CRITICAL: AI Image Fix - Fixed "AI service not configured" error (now uses correct API key)
* NEW: Payment Button section - Accept payments directly on landing pages
* NEW: Separator/Divider section - Add visual breaks between sections
* NEW: Text Section styles - Light, Gradient, Dark backgrounds
* NEW: Products Section styles - Light, Gradient, Dark backgrounds
* NEW: CTA Button width control - Inline or Full-width options
* NEW: WhatsApp icon - SVG WhatsApp icon on WhatsApp buttons and sticky bar
* NEW: Digital Product template - For selling ebooks/courses with payment
* NEW: Marketplace transactions database table
* IMPROVED: Add Section menu - Smaller grid, Cancel (×) button, cleaner layout
* IMPROVED: Sticky Bar - Added Link option alongside WhatsApp and Call
* REMOVED: Ebook section from Add Section menu (use Digital Product template instead)
* FIXED: Edit panel blank page bug when selecting sections
* FIXED: Old URLs now 301 redirect to clean URLs automatically

= 1.8.3 =
* FIXED: AI Image page 404 - pages now use clean URLs (e.g. /ai-image not /hushot-ai-image)
* NEW: AI Image Generator - Generate 3 professional product images from photo/prompt
* NEW: Marketplace Payment System - Sellers can receive payments with automatic split
* NEW: Seller Dashboard - View earnings, transactions, commission breakdown
* NEW: Seller Setup - Bank account verification and Flutterwave subaccounts
* NEW: Plan-based template restrictions (Free: Starter only, Paid: All templates)
* NEW: Plan-based feature restrictions (Form section requires Essential+)
* NEW: "Promote" checkbox on publish - Changes button to "Boost Now" → redirects to ads
* NEW: Double-tap to edit section, single tap to select (improved mobile UX)
* CHANGED: Dashboard quick action - Replaced "Templates" with "AI Image"
* CHANGED: Pricing page - Now shows Free plan first, yearly billing default
* FIXED: Critical error on published pages (stray PHP syntax)
* FIXED: Plan limits - Free: 1 page, Essential: 5 pages, Premium: Unlimited

= 1.8.1 =
* CRITICAL: Visual Builder = Live Page (100% match between builder and published page)
* NEW: Banner Section - Combined headline, subheadline, image, and button in one section
* NEW: Form Section - Name, Email, Phone fields with ability to add custom fields
* NEW: Lead Gen Template - Optimized for capturing leads with form
* FIXED: Edit panel no longer covers page content (moved to side panel)
* FIXED: AI Builder mobile layout - Edit button no longer causes horizontal overflow
* FIXED: Create Page typography - Removed underlines, increased text sizes
* IMPROVED: iOS Install Experience - Visual step-by-step guide with icons
* IMPROVED: Template Picker - 5 templates including new Lead Gen option
* IMPROVED: Add Section menu - Now includes Banner, Form sections
* IMPROVED: Sticky Bottom Bar support in Visual Builder

= 1.8.0 =
* CRITICAL: PWA App Icon & Splash - Now properly displays on all devices (iOS + Android)
* CRITICAL: All icon sizes regenerated from official brand logo (72-512px)
* REDESIGNED: Visual Builder - Multiple design templates with preview (Starter, Product, Service, Minimal)
* REDESIGNED: Section management - Add, remove, duplicate, reorder sections
* REDESIGNED: Desktop layout - Fixed sidebar spacing and alignment issues
* NEW: Video upload support in all sections
* NEW: Complete CTA options (WhatsApp, Phone Call, External Link)
* NEW: Template picker with visual previews before selection
* FIXED: Analytics double-counting - Session-based deduplication (30-min window)
* FIXED: Mobile scrolling in visual builder - Preview area now scrolls freely
* FIXED: Hero image upload visibility on mobile
* FIXED: Desktop UI scattered menu items and inconsistent spacing
* FIXED: apple-touch-icon all sizes for iOS home screen
* FIXED: apple-touch-startup-image now uses actual splash screen
* IMPROVED: AI Builder layout stability
* IMPROVED: Mobile-first design throughout all pages
* IMPROVED: Sidebar with brand logo and better navigation labels
* IMPROVED: Edit panel swipe-down to close on mobile
* IMPROVED: Service worker updated to cache icons properly

= 1.7.1 =
* REDESIGNED: Create Page now offers builder choice (Visual or Component)
* REDESIGNED: Visual Builder - Mobile-first with bottom sheet panel on mobile, side panel on desktop
* REDESIGNED: Install App Page - Sticky header, brand logo/splash screen, improved iOS instructions
* REDESIGNED: AI Builder - Compact style grid cards, responsive URL import section
* FIXED: URL import button alignment on mobile
* FIXED: Style options now display as compact 4-column grid
* REMOVED: Tips section (Be Specific, Clear CTA, Contact) from AI Builder
* IMPROVED: Brand colors updated (Orange #F7931E theme)
* IMPROVED: PWA icons now use official Hushot logo
* IMPROVED: Manifest.json with proper splash screen and theme colors

= 1.7.0 =
* NEW: Visual Builder - Live preview page editing with instant updates
* NEW: Website URL Import - AI extracts business info from existing websites
* NEW: Install App Public Page - Shareable PWA install link
* NEW: Smart Install App Sticky Header for logged-in users only
* FIXED: PWA app icons now display correctly after installation
* FIXED: iOS dashboard menu compatibility
* FIXED: Install tracking now works properly
* IMPROVED: Multiple section style options (3 styles per section)
* IMPROVED: iOS Safari full compatibility
* IMPROVED: Mobile touch event handling

= 1.6.3 =
* Essential plan CTA fix
* Form visibility bug resolution
* Page customization controls
* AI image generation with DALL-E
* PWA install UI improvements

= 1.2.3 =
* Professional UI for login, register, forgot password pages
* Dashboard: Removed white space, reduced header by 30%
* Templates: Removed large "Use" button, only small "Use template" button
* Edit page: Fixed to show edit form instead of published view
* Component locking: Free plan gets basics (Header, Image, Details, Features, Price, WhatsApp, Contact)
* Paid features: Video, Form, Link, Sticky Bar, More Products
* Products section: Accordion for description, expandable image on tap
* Products pricing: Old price strikethrough, new price highlighted
* Essential plan: 3 products limit
* Premium plan: Unlimited products
* Fixed Flutterwave initialization

= 1.2.2 =
* 13 user-requested fixes
* Compact header, inline pricing
* Products with description
* Bottom bar conditional inputs

= 1.2.1 =
* Fixed template cards
* Theme override improvements

= 1.2.0 =
* Complete responsive redesign
* 11 new features
