-- SQL Updates to Change Hotel Wording from Luxury to Simple 2-Star Hotel
-- This file updates all luxury/premium language to simple, straightforward language

-- ============================================
-- 1. UPDATE HERO SLIDES
-- ============================================

UPDATE hero_slides SET 
  title = 'Welcome to Liwonde Sun Hotel',
  subtitle = 'Your Comfortable Stay in Malawi',
  description = 'Enjoy a pleasant and affordable stay with us. Clean rooms, friendly service, and great value for money.',
  primary_cta_text = 'Book a Room',
  secondary_cta_text = 'View Rooms'
WHERE id = 1;

UPDATE hero_slides SET 
  title = 'Beautiful River Views',
  subtitle = 'Scenic Surroundings',
  description = 'Wake up to lovely views of the Shire River and enjoy the peaceful atmosphere of our hotel.',
  primary_cta_text = 'See Gallery',
  secondary_cta_text = 'Plan Your Stay'
WHERE id = 2;

UPDATE hero_slides SET 
  title = 'Good Food & Drinks',
  subtitle = 'Tasty Local & International Cuisine',
  description = 'Enjoy satisfying meals prepared with care. Our restaurant offers a variety of dishes at reasonable prices.',
  primary_cta_text = 'View Menu',
  secondary_cta_text = 'Contact Us'
WHERE id = 3;

UPDATE hero_slides SET 
  title = 'Relax and Unwind',
  subtitle = 'Comfortable Facilities',
  description = 'Take a dip in our pool, work out in the gym, or simply relax in our comfortable common areas.',
  primary_cta_text = 'Explore Facilities',
  secondary_cta_text = 'Book Now'
WHERE id = 4;

-- ============================================
-- 2. UPDATE ABOUT US SECTION
-- ============================================

UPDATE about_us SET 
  title = 'Welcome to Liwonde Sun Hotel',
  subtitle = 'Our Story',
  content = 'Located in the heart of Malawi, Liwonde Sun Hotel offers comfortable and affordable accommodation for travelers. We provide clean rooms, friendly service, and good value for money. Our hotel is perfect for budget-conscious travelers who want a pleasant stay without breaking the bank.'
WHERE id = 1;

UPDATE about_us SET 
  title = 'Friendly Service',
  content = 'Our staff is dedicated to making your stay comfortable and pleasant'
WHERE id = 2;

UPDATE about_us SET 
  title = 'Great Location',
  content = 'Conveniently located near Liwonde National Park and local attractions'
WHERE id = 3;

UPDATE about_us SET 
  title = 'Comfortable Rooms',
  content = 'Clean and well-maintained rooms for a good night''s rest'
WHERE id = 4;

UPDATE about_us SET 
  title = 'Good Value',
  content = 'Affordable rates with everything you need for a comfortable stay'
WHERE id = 5;

UPDATE about_us SET 
  stat_number = '10+',
  stat_label = 'Years Serving Guests'
WHERE id = 6;

UPDATE about_us SET 
  stat_number = '95%',
  stat_label = 'Guest Satisfaction'
WHERE id = 7;

-- ============================================
-- 3. UPDATE FACILITIES
-- ============================================

UPDATE facilities SET 
  name = 'Restaurant',
  description = 'Our restaurant serves tasty local and international dishes. Open for breakfast, lunch, and dinner. Enjoy good food at affordable prices.',
  short_description = 'Good food at reasonable prices',
  icon_class = 'fas fa-utensils'
WHERE id = 1;

UPDATE facilities SET 
  name = 'Swimming Pool',
  description = 'Outdoor swimming pool perfect for cooling off and relaxing. Pool area with seating available.',
  short_description = 'Refreshing outdoor pool',
  icon_class = 'fas fa-swimming-pool'
WHERE id = 3;

UPDATE facilities SET 
  name = 'Fitness Center',
  description = 'Well-equipped gym with cardio machines and weights. Open daily for hotel guests.',
  short_description = 'Exercise facilities available',
  icon_class = 'fas fa-dumbbell'
WHERE id = 4;

UPDATE facilities SET 
  name = 'WiFi Internet',
  description = 'Complimentary WiFi available throughout the hotel for all guests.',
  short_description = 'Free internet access',
  icon_class = 'fas fa-wifi'
WHERE id = 5;

UPDATE facilities SET 
  name = 'Front Desk Service',
  description = 'Our front desk is available to help with check-in, information, and assistance during your stay.',
  short_description = 'Helpful front desk staff',
  icon_class = 'fas fa-concierge-bell'
WHERE id = 6;

-- ============================================
-- 4. UPDATE ROOM DESCRIPTIONS
-- ============================================

UPDATE rooms SET 
  name = 'Executive Suite',
  slug = 'executive-suite',
  description = 'Comfortable suite with separate sitting area. Includes a desk for work, TV, WiFi, coffee/tea facilities, and mini fridge. Good for business travelers or those wanting extra space.',
  short_description = 'Spacious room with work area'
WHERE id = 2;

UPDATE rooms SET 
  name = 'Deluxe Room',
  slug = 'deluxe-room',
  description = 'Comfortable room with en-suite bathroom. Features a comfortable bed, TV, WiFi, and basic amenities. Clean and well-maintained for a good night''s sleep.',
  short_description = 'Comfortable room with private bathroom'
WHERE id = 4;

UPDATE rooms SET 
  name = 'Standard Room',
  slug = 'standard-room',
  description = 'Simple, clean room with everything you need. Comfortable bed, TV, and WiFi. Perfect for budget travelers looking for a good value.',
  short_description = 'Simple, affordable accommodation'
WHERE id = 5;

-- ============================================
-- 5. UPDATE PAGE HEROES
-- ============================================

UPDATE page_heroes SET 
  hero_title = 'Restaurant & Bar',
  hero_subtitle = 'Good Food & Drinks',
  hero_description = 'Enjoy tasty meals and refreshing drinks at our restaurant. We serve local and international dishes at reasonable prices.'
WHERE page_slug = 'restaurant';

UPDATE page_heroes SET 
  hero_title = 'Conference & Meeting Room',
  hero_subtitle = 'Event Space Available',
  hero_description = 'We have meeting rooms available for conferences, workshops, and events. Basic amenities included.'
WHERE page_slug = 'conference';

UPDATE page_heroes SET 
  hero_title = 'Events',
  hero_subtitle = 'What''s Happening',
  hero_description = 'Join us for special events and activities at the hotel.'
WHERE page_slug = 'events';

UPDATE page_heroes SET 
  hero_title = 'Our Rooms',
  hero_subtitle = 'Comfortable Accommodation',
  hero_description = 'Choose from our range of clean, comfortable rooms at affordable prices.'
WHERE page_slug = 'rooms-showcase' OR page_slug = 'rooms-gallery';

UPDATE page_heroes SET 
  hero_title = 'Fitness Center',
  hero_subtitle = 'Stay Active',
  hero_description = 'Our gym has basic equipment for your workout needs.'
WHERE page_slug = 'gym';

-- ============================================
-- 6. UPDATE SITE SETTINGS
-- ============================================

UPDATE site_settings SET 
  setting_value = 'Where Comfort Meets Value'
WHERE setting_key = 'site_tagline';

UPDATE site_settings SET 
  setting_value = 'Your Comfortable Stay in Malawi'
WHERE setting_key = 'hero_title';

UPDATE site_settings SET 
  setting_value = 'Enjoy a pleasant and affordable stay with clean rooms, friendly service, and good value for money.'
WHERE setting_key = 'hero_subtitle';

UPDATE site_settings SET 
  setting_value = 'hotel malawi, liwonde accommodation, budget hotel, affordable stay, malawi lodging'
WHERE setting_key = 'default_keywords';

-- ============================================
-- 7. UPDATE CONFERENCE ROOMS
-- ============================================

UPDATE conference_rooms SET 
  description = 'Small meeting room suitable for business meetings and presentations. Includes basic presentation equipment.'
WHERE id = 1 OR id = 4;

UPDATE conference_rooms SET 
  description = 'Large conference space for seminars, workshops, and corporate events. Can be divided for smaller groups.'
WHERE id = 2 OR id = 5;

UPDATE conference_rooms SET 
  description = 'Meeting room with nice views. Good for training sessions and medium-sized gatherings.'
WHERE id = 3 OR id = 6;

-- ============================================
-- 8. UPDATE GYM CONTENT
-- ============================================

UPDATE gym_content SET 
  hero_title = 'Fitness Center',
  hero_subtitle = 'Stay Active',
  hero_description = 'Our fitness center has the equipment you need to maintain your workout routine while traveling.',
  wellness_title = 'Exercise Facilities',
  wellness_description = 'We offer basic gym equipment for cardio and strength training. Available to all hotel guests.',
  badge_text = 'Fitness Facilities Available'
WHERE id = 4;

-- ============================================
-- 9. UPDATE POLICIES
-- ============================================

UPDATE policies SET 
  summary = 'Simple booking terms',
  content = 'Bookings can be made by phone or email. A deposit may be required to confirm your reservation. Please contact us for changes to your booking.'
WHERE slug = 'booking-policy';

UPDATE policies SET 
  summary = 'Fair cancellation terms',
  content = 'Cancellations made at least 48 hours before arrival will receive a full refund. Cancellations within 48 hours may be charged one night.'
WHERE slug = 'cancellation-policy';

-- ============================================
-- 10. UPDATE TESTIMONIALS (keeping reasonable ones)
-- ============================================

UPDATE testimonials SET 
  testimonial_text = 'Nice hotel with friendly staff. Rooms were clean and comfortable. Good value for money. Would stay again.'
WHERE id = 1;

UPDATE testimonials SET 
  testimonial_text = 'Pleasant stay in a good location. Staff was helpful and the rooms were tidy. Simple but comfortable.'
WHERE id = 2;

UPDATE testimonials SET 
  testimonial_text = 'Good budget hotel option. Clean rooms, decent food, and friendly service. Met our expectations for a 2-star hotel.'
WHERE id = 3;

-- ============================================
-- 11. UPDATE HOTEL GALLERY DESCRIPTIONS
-- ============================================

UPDATE hotel_gallery SET 
  title = 'Hotel Front View',
  description = 'Welcome to Liwonde Sun Hotel'
WHERE id = 1;

UPDATE hotel_gallery SET 
  title = 'Pool Area',
  description = 'Our outdoor swimming pool'
WHERE id = 2;

UPDATE hotel_gallery SET 
  title = 'Restaurant',
  description = 'Our dining area'
WHERE id = 3;

UPDATE hotel_gallery SET 
  title = 'Guest Room',
  description = 'Comfortable room with private bathroom'
WHERE id = 4;

-- ============================================
-- END OF UPDATES
-- ============================================