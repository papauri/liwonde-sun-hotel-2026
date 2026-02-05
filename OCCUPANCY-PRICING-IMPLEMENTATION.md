# Single/Double Occupancy Pricing Implementation

## Overview
This document describes the implementation of single and double occupancy pricing for the Liwonde Sun Hotel booking system.

## Implementation Date
February 5, 2026

## Pricing Structure

### Executive Suite (Room ID: 2)
- **Single Occupancy (1 guest):** Mk 130,000
- **Double Occupancy (2 guests):** Mk 165,000

### Deluxe Suite (Room ID: 4)
- **Single Occupancy (1 guest):** Mk 100,000
- **Double Occupancy (2 guests):** Mk 135,000

### Superior Room (Room ID: 5)
- **Single Occupancy (1 guest):** Mk 85,000
- **Double Occupancy (2 guests):** Mk 115,000

## Database Changes

### New Columns Added to `rooms` Table:
```sql
price_single_occupancy DECIMAL(10,2) - Price for single occupancy
price_double_occupancy DECIMAL(10,2) - Price for double occupancy  
price_triple_occupancy DECIMAL(10,2) - Price for triple occupancy (future use)
```

### New Column Added to `bookings` Table:
```sql
occupancy_type ENUM('single', 'double', 'triple') - Stores occupancy type for booking
```

## Migration File
Location: `Database/migrations/add_occupancy_pricing.sql`

### What the migration does:
1. Adds new columns to rooms and bookings tables
2. Updates existing rooms with new pricing structure
3. Sets default occupancy type for existing bookings to 'double'
4. Logs the migration in migration_log table

### Running the Migration:
```bash
# Via phpMyAdmin
# Import the file: Database/migrations/add_occupancy_pricing.sql

# Via MySQL CLI
mysql -u username -p database_name < Database/migrations/add_occupancy_pricing.sql

# Via PHP script (if you have CLI access)
php -r "require 'config/database.php'; $sql = file_get_contents('Database/migrations/add_occupancy_pricing.sql'); $pdo->exec($sql);"
```

## Code Changes

### 1. Booking Form (booking.php)
- **Backend:** Added occupancy-based pricing logic
  - Reads occupancy_type from POST data
  - Calculates price based on selected occupancy type
  - Stores occupancy_type in bookings table
  
- **Frontend:** Added occupancy selection UI
  - Two radio buttons for Single/Double occupancy
  - Dynamic price display based on selection
  - Visual feedback for selected option

- **JavaScript:** Added occupancy pricing functions
  - `updateOccupancyPricing(roomId)` - Updates price displays
  - `updatePriceForOccupancy()` - Changes selected price based on occupancy type
  - Rooms data now includes occupancy pricing

## How It Works

### User Flow:
1. User selects a room
2. System displays both Single and Double occupancy prices
3. User selects occupancy type (Single or Double)
4. Booking summary updates with correct price
5. Form submits with selected occupancy type
6. Backend calculates total based on occupancy pricing
7. Booking saved with occupancy_type and correct total_amount

### Price Calculation Logic:
```php
if ($occupancy_type === 'single' && !empty($room['price_single_occupancy'])) {
    $room_price = $room['price_single_occupancy'];
} elseif ($occupancy_type === 'double' && !empty($room['price_double_occupancy'])) {
    $room_price = $room['price_double_occupancy'];
} elseif ($occupancy_type === 'triple' && !empty($room['price_triple_occupancy'])) {
    $room_price = $room['price_triple_occupancy'];
} else {
    // Fallback to default price
    $room_price = $room['price_per_night'];
}

$total_amount = $room_price * $number_of_nights;
```

## Benefits

### For Guests:
- Clear pricing options based on occupancy
- Single travelers pay less
- Transparent pricing structure
- Easy to compare options

### For Hotel:
- Better revenue management
- Competitive single occupancy rates
- Flexible pricing strategy
- Detailed booking data for analytics

## Testing Checklist

### Database:
- [ ] Run migration script
- [ ] Verify columns added to rooms table
- [ ] Verify columns added to bookings table
- [ ] Check pricing data is correct for Executive, Deluxe, and Superior rooms

### Frontend:
- [ ] Occupancy selection appears on booking form
- [ ] Prices update when occupancy type changes
- [ ] Visual feedback works (border color change)
- [ ] Booking summary shows correct total

### Backend:
- [ ] Form submits with occupancy_type
- [ ] Total calculated correctly based on occupancy
- [ ] Booking saved with correct data
- [ ] Email notifications show correct pricing

### Integration:
- [ ] Admin panel can edit occupancy prices
- [ ] Reports show occupancy-based bookings
- [ ] Invoice generation works with new pricing
- [ ] API endpoints return occupancy pricing

## Future Enhancements

### Potential Additions:
1. Triple occupancy pricing for family rooms
2. Dynamic pricing based on season/demand
3. Corporate rates for single occupancy
4. Package deals with meal plans
5. Long-stay discounts by occupancy type

### Admin Panel Updates Needed:
- Add fields to edit occupancy prices in room management
- Show occupancy breakdown in reports
- Filter bookings by occupancy type
- Revenue analytics by occupancy type

## Rollback Plan

If issues arise, you can rollback by:

1. **Database:**
```sql
ALTER TABLE rooms DROP COLUMN price_single_occupancy;
ALTER TABLE rooms DROP COLUMN price_double_occupancy;
ALTER TABLE rooms DROP COLUMN price_triple_occupancy;
ALTER TABLE bookings DROP COLUMN occupancy_type;
```

2. **Code:**
   - Revert booking.php to previous version
   - Remove occupancy selection UI
   - Remove occupancy pricing logic from backend

## Support

For questions or issues:
- Check the migration log: `SELECT * FROM migration_log WHERE migration_name = 'occupancy_pricing_system'`
- Review database structure: `DESCRIBE rooms;` and `DESCRIBE bookings;`
- Test booking flow in development environment first

## Notes

- The `price_per_night` column is kept as fallback
- Existing bookings automatically set to 'double' occupancy
- Rooms without specific occupancy pricing use calculated values (single = price_per_night, double = price_per_night * 1.2)
- The system is backward compatible with existing functionality