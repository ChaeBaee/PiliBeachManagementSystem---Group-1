# Enhanced Room Availability Checking

## Overview

The room availability checking system has been enhanced to allow same-day bookings when there's sufficient time between checkout and check-in times. This feature enables more efficient room utilization while ensuring proper cleaning and preparation time.

## How It Works

### Traditional Availability Checking
Previously, the system would block any booking that overlapped with existing bookings, even if they were on the same day with sufficient time gaps.

### Enhanced Availability Checking
The new system considers the actual time gap between checkout and check-in times:

1. **Same-day bookings are allowed** if there's at least **2 hours** between:
   - Previous guest's checkout time and new guest's check-in time
   - New guest's checkout time and next guest's check-in time

2. **Overlapping dates are still blocked** to prevent double bookings

3. **Time gap calculation** considers both date and time components

## Implementation Details

### Key Files Modified

1. **`PHP/booking/back/check_availability.php`**
   - Enhanced `checkAvailabilityWithTimeGap()` function
   - Considers `booking_checkin_time` and `booking_checkout_time` fields

2. **`PHP/booking/back/booking_confirmation_backend.php`**
   - Updated `validateBookingDates()` function
   - Enhanced final availability check with time gap consideration

3. **`PHP/booking/front/room-details.php`**
   - Updated frontend to pass `checkin_time` parameter
   - Enhanced availability status messages

4. **`Admin/Bookings/back/bookings_backend.php`**
   - Updated `checkRoomAvailability()` method
   - Updated `getAvailableRooms()` method
   - Updated `addBooking()` and `updateBooking()` methods

5. **`Admin/Bookings/front/bookings.php`**
   - Updated `loadAvailableRooms()` function to include checkin time

### Database Requirements

The system requires the following fields in the `bookings` table:
- `booking_checkin_time` (TIME) - Default: '14:00'
- `booking_checkout_time` (TIME) - Default: '12:00'

## Usage Examples

### Scenario 1: Same-day booking with sufficient gap
- **Existing booking**: Checkout on 2024-01-15 at 12:00 PM
- **New booking**: Check-in on 2024-01-15 at 2:00 PM
- **Result**: ✅ **ALLOWED** (2-hour gap)

### Scenario 2: Same-day booking with insufficient gap
- **Existing booking**: Checkout on 2024-01-15 at 12:00 PM
- **New booking**: Check-in on 2024-01-15 at 1:00 PM
- **Result**: ❌ **BLOCKED** (1-hour gap, need at least 2 hours)

### Scenario 3: Different day booking
- **Existing booking**: Checkout on 2024-01-15 at 12:00 PM
- **New booking**: Check-in on 2024-01-16 at 2:00 PM
- **Result**: ✅ **ALLOWED** (different days)

### Scenario 4: Overlapping dates
- **Existing booking**: 2024-01-15 to 2024-01-17
- **New booking**: 2024-01-16 to 2024-01-18
- **Result**: ❌ **BLOCKED** (overlapping dates)

## Configuration

### Minimum Time Gap
The minimum required time gap is currently set to **2 hours**. This can be adjusted by modifying the condition in the availability checking functions:

```php
if ($gap_hours >= 2) {
    // Allow booking
} else {
    // Block booking
}
```

### Default Times
- **Default check-in time**: 14:00 (2:00 PM)
- **Default check-out time**: 12:00 (12:00 PM)

## Testing

A test file `test_enhanced_availability.php` is provided to verify the functionality:

```bash
# Run the test
php test_enhanced_availability.php
```

The test includes various scenarios to ensure the system works correctly.

## Benefits

1. **Increased Revenue**: More efficient room utilization
2. **Better Guest Experience**: More booking options available
3. **Operational Efficiency**: Allows for same-day turnovers
4. **Flexibility**: Configurable time gap requirements

## Considerations

1. **Cleaning Time**: Ensure sufficient time for room cleaning between guests
2. **Staff Scheduling**: Consider housekeeping staff availability
3. **Guest Communication**: Clearly communicate check-in/check-out times
4. **Maintenance**: Allow time for any necessary room maintenance

## Future Enhancements

Potential improvements could include:
- Dynamic time gap requirements based on room type
- Integration with housekeeping schedules
- Real-time availability updates
- Advanced booking patterns analysis 