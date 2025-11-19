# Booking Cancellation System

## Overview
The booking cancellation functionality has been separated into its own dedicated page for better user experience and easier management.

## Files

### Main Cancellation Page
- **`cancel-booking.php`** - Standalone cancellation page with full UI and functionality

### Updated Files
- **`profile.php`** - Updated to include links to the cancellation page
- **`../elements/navbar.php`** - Added navigation links for the cancellation page

### Removed Files
- **`back/cancel-booking.php`** - Old backend file (functionality moved to main page)

## Features

### User Interface
- **Dedicated Cancellation Page**: Clean, focused interface for managing bookings
- **Cancellation Policy Display**: Clear policy information before cancellation
- **Booking Status Indicators**: Visual status badges for different booking states
- **Confirmation Modal**: Enhanced confirmation dialog with policy reminders
- **Responsive Design**: Works on desktop and mobile devices

### Functionality
- **Booking Verification**: Ensures users can only cancel their own bookings
- **Status Updates**: Updates booking, room, payment, and invoice statuses
- **Transaction Safety**: Uses database transactions for data integrity
- **Error Handling**: Comprehensive error handling and user feedback
- **Navigation Integration**: Easy access from profile page and navbar

### Security Features
- **Session Validation**: Ensures only logged-in users can access
- **User Authorization**: Users can only cancel their own bookings
- **SQL Injection Prevention**: Uses prepared statements
- **CSRF Protection**: Form-based submission with proper validation

## User Flow

1. **Access**: Users can access the cancellation page from:
   - Profile page (via "Cancel Bookings" button)
   - Navigation bar (via "Cancel Booking" link)

2. **Review**: Users see:
   - Cancellation policy
   - List of their bookings with status
   - Booking details (room, dates, amount)

3. **Cancel**: Users can:
   - Click "Cancel Booking" for eligible bookings
   - See confirmation modal with policy reminders
   - Confirm or cancel the action

4. **Feedback**: Users receive:
   - Success/error messages
   - Updated booking status
   - Automatic page refresh

## Database Updates

When a booking is cancelled, the system updates:

1. **Bookings Table**: `booking_status = 'Canceled'`
2. **Rooms Table**: `room_status = 'available'`
3. **Payments Table**: `payment_status = 'failed'`
4. **Invoice Table**: `invoice_status = 'canceled'`

## Cancellation Policy

The system enforces the following policy:
- No refunds for cancelled bookings
- Cancellation fees may apply
- Once cancelled, bookings cannot be reinstated
- Rooms are made available for other guests

## Technical Implementation

### Frontend
- **Tailwind CSS**: Modern, responsive styling
- **Font Awesome**: Icons for better UX
- **JavaScript**: Modal interactions and form handling
- **PHP**: Server-side logic and database operations

### Backend
- **PDO**: Database operations with prepared statements
- **Transactions**: Data integrity during cancellation
- **Session Management**: User authentication and authorization
- **Error Handling**: Comprehensive exception handling

## Navigation Integration

### Desktop Navigation
- Profile button (blue border)
- Cancel Booking button (red border)
- Logout button (red background)

### Mobile Navigation
- Profile link
- Cancel Booking link (red text)
- Logout link (red text)

## Future Enhancements

Potential improvements for the cancellation system:
- Email notifications for cancellations
- Cancellation history tracking
- Partial refund calculations
- Admin approval workflow
- Cancellation reason tracking 