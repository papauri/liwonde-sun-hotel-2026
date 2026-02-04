# Restaurant Menu Update - February 2026
**Date:** February 5, 2026  
**Status:** ✅ Completed Successfully

## Summary
Successfully updated the Liwonde Sun Hotel restaurant menu from the PDF document and added all phone numbers to site settings.

## Changes Made

### 1. Database Cleanup
- **Deleted 24 existing menu items** from the `food_menu` table
- **Deleted 5 old categories** from the `menu_categories` table

### 2. Menu Categories Created (13 total)
1. Breakfast
2. Starter
3. Chicken Corner
4. Meat Corner
5. Fish Corner
6. Pasta Corner
7. Burger Corner
8. Pizza Corner
9. Snack Corner
10. Indian Corner
11. Liwonde Sun Specialities
12. Extras
13. Desserts

### 3. Menu Items Added (71 total)
All menu items were imported from the PDF with proper pricing in Malawian Kwacha (MWK) and organized by category:

---

**BREAKFAST**
├── English Breakfast - MK35,000.00

**STARTER**
├── Mushroom Soup - MK15,000.00
├── Italian Style Tomato Soup - MK15,000.00
├── Green Salad - MK10,000.00
├── Sun Hotel Greek Salad - MK12,000.00
├── Tempura Prawns - MK17,000.00
├── Chicken liver Masala - MK17,000.00
└── Hot Snack Platter - MK20,500.00

**CHICKEN CORNER**
├── Chicken peri-peri - MK22,000.00
├── Boiled Chicken Curry - MK22,000.00
├── Chicken Stir-fry - MK22,000.00
├── Grilled ¼ Chicken - MK22,000.00
├── Chicken Khwasu - MK22,000.00
└── Local Chicken - MK22,000.00

**MEAT CORNER**
├── T Bone Steak - MK28,000.00
├── Beef Strips - MK20,000.00
├── Sirloin Steak - MK24,000.00
├── Goat Stew - MK18,000.00
├── Beef Stew - MK19,000.00
└── Fillet Mignon - MK35,000.00

**FISH CORNER**
├── Fish & Chips - MK30,000.00
├── Grilled Chambo (open & whole) - MK28,000.00
├── Grilled Kampango - MK28,000.00
├── Mama's Choice - MK28,000.00
└── Sun prawn platter - MK48,000.00

**PASTA CORNER**
├── Spaghetti Bolognese - MK25,000.00
├── Spaghetti Napolitano - MK18,000.00
├── Chicken Alfredo - MK20,000.00
└── Asian Vegetables Stir fly - MK22,000.00

**BURGER CORNER**
├── Sun Hotel Burger - MK20,000.00
├── Mega Double Burger - MK30,000.00
└── Chicken Spice Burger - MK25,000.00

**PIZZA CORNER**
├── Barbeque Pizza Large - MK36,000.00
├── Barbeque Pizza Medium - MK32,000.00
├── Barbeque Pizza Small - MK28,000.00
├── Vegetable Pizza Large - MK30,000.00
├── Vegetable Pizza Medium - MK25,000.00
├── Vegetable Pizza Small - MK22,000.00
├── Chicken & Boerewors Pizza Large - MK35,000.00
├── Chicken & Boerewors Pizza Medium - MK30,000.00
├── Chicken & Boerewors Pizza Small - MK28,000.00
└── Extra-Large Pizza (All varieties) - MK42,000.00

**SNACK CORNER**
├── Cajun Chicken Wings or Drum sticks - MK15,500.00
├── Meat Balls - MK20,000.00
├── Beef Samosa or Chicken Samosa - MK18,000.00
├── Chicken wrap or Beef Wraps - MK19,000.00
├── Chicken Fingers - MK15,500.00
├── Deli-style Sandwich - MK18,000.00
├── Sausages - MK16,000.00
├── Omelette or fried Eggs - MK12,000.00
├── Plain chips - MK10,000.00
└── Chicken Chiwamba (whole) - MK40,000.00

**INDIAN CORNER**
├── Fish Curry - MK28,000.00
├── Chicken Butter - MK23,500.00
├── Beef Curry - MK24,500.00
├── Goat Curry - MK24,500.00
└── Biriyani Rice - MK27,000.00

**LIWONDE SUN SPECIALITIES**
├── Jollof Rice - MK34,000.00
└── Okra Soup - MK34,000.00

**EXTRAS**
├── Plain Chapati - MK7,000.00
├── Plain Nsima - MK7,000.00
├── Plain Rice - MK7,000.00
├── Plain Chips - MK10,000.00
├── Beef or Chicken Samosa Only (4) - MK10,000.00
└── Extra Vegetable/Beans - MK7,000.00

**DESSERTS**
├── Banana Custard - MK15,000.00
├── Milk Shake - MK8,500.00
├── Fruit of the Day - MK5,000.00
├── Ice Cream Cup - MK5,000.00
├── Chocolate Gateaux - MK15,000.00
└── Fruit Salads (bowl) - MK12,000.00

---

### 4. Site Settings Updated (8 phone numbers + 1 email)

#### Phone Numbers Added:
- **phone_main:** 0212 877 796
- **phone_reception:** 0883 500 304
- **phone_cell1:** 0998 864 377
- **phone_cell2:** 0882 363 765
- **phone_alternate1:** 0983 825 196
- **phone_alternate2:** 0999 877 796
- **phone_alternate3:** 0888 353 540

#### Email Added:
- **email_restaurant:** liwondesunhotel@gmail.com

## Technical Details

### Migration File
- **Location:** `Database/migrations/update_menu_feb_2026.php`
- **Database:** p601229_hotels (production)
- **Host:** promanaged-it.com
- **Execution Time:** February 5, 2026, 8:24 AM

### Database Tables Modified
1. **food_menu** - Cleared and repopulated with 71 items
2. **menu_categories** - Cleared and repopulated with 13 categories
3. **site_settings** - Updated with 8 phone numbers and 1 email

### Data Format
- All prices stored as decimal/float values
- Currency code: MWK (Malawian Kwacha)
- All items set as available (is_available = 1)
- All items set as non-featured (is_featured = 0)
- Proper display order maintained for all categories and items

## Verification

The menu update was successful:
- ✅ All old menu data cleared
- ✅ All new categories created
- ✅ All menu items imported with correct pricing
- ✅ All phone numbers added to site settings
- ✅ Restaurant email added
- ✅ Database transaction committed successfully

## Next Steps

1. **Test the menu display** on the restaurant page (`/restaurant.php`)
2. **Verify phone numbers** are displayed correctly in the header and footer
3. **Update any cached data** if the site uses caching
4. **Consider adding featured items** - currently all items are marked as non-featured
5. **Review menu descriptions** for any typos or improvements

## Notes

- The `data/menu.json` file is for website navigation, NOT the food menu
- The food menu is stored in the database tables: `food_menu` and `menu_categories`
- The admin panel at `/admin/menu-management.php` can be used to manage the menu going forward
- All prices are in Malawian Kwacha (MWK)
- Menu items can be marked as featured by setting `is_featured = 1` in the database or via admin

---
**Migration executed by:** System Administrator  
**Backup recommendation:** Always create database backups before running migrations