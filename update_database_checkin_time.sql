-- Add check-in time and check-out time fields to bookings table
ALTER TABLE `bookings` 
ADD COLUMN `booking_checkin_time` TIME DEFAULT '14:00:00' AFTER `booking_check_in`,
ADD COLUMN `booking_checkout_time` TIME DEFAULT '12:00:00' AFTER `booking_check_out`;

-- Update existing records to have default times
UPDATE `bookings` SET 
`booking_checkin_time` = '14:00:00',
`booking_checkout_time` = '12:00:00';

-- Update the audit log triggers to include the new time fields
DELIMITER $$

DROP TRIGGER IF EXISTS `trg_bookings_insert`$$
CREATE TRIGGER `trg_bookings_insert` AFTER INSERT ON `bookings` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'bookings',
    NEW.booking_id,
    'INSERT',
    NULL,
    CONCAT(
      'user_id: ', NEW.user_id, ', ',
      'room_id: ', NEW.room_id, ', ',
      'check_in: ', NEW.booking_check_in, ' ', NEW.booking_checkin_time, ', ',
      'check_out: ', NEW.booking_check_out, ' ', NEW.booking_checkout_time, ', ',
      'total_price: ', NEW.booking_total_price, ', ',
      'booking_status: ', NEW.booking_status
    ),
    @current_user_id  
  );
END$$

DROP TRIGGER IF EXISTS `trg_bookings_update`$$
CREATE TRIGGER `trg_bookings_update` AFTER UPDATE ON `bookings` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'bookings',
    NEW.booking_id,
    'UPDATE',
    CONCAT(
      'user_id: ', OLD.user_id, ', ',
      'room_id: ', OLD.room_id, ', ',
      'booking_check_in: ', OLD.booking_check_in, ' ', OLD.booking_checkin_time, ', ',
      'booking_check_out: ', OLD.booking_check_out, ' ', OLD.booking_checkout_time, ', ',
      'booking_total_price: ', OLD.booking_total_price, ', ',
      'booking_status: ', OLD.booking_status
    ),
    CONCAT(
      'user_id: ', NEW.user_id, ', ',
      'room_id: ', NEW.room_id, ', ',
      'booking_check_in: ', NEW.booking_check_in, ' ', NEW.booking_checkin_time, ', ',
      'booking_check_out: ', NEW.booking_check_out, ' ', NEW.booking_checkout_time, ', ',
      'booking_total_price: ', NEW.booking_total_price, ', ',
      'booking_status: ', NEW.booking_status
    ),
    @current_user_id  
  );
END$$

DROP TRIGGER IF EXISTS `trg_bookings_delete`$$
CREATE TRIGGER `trg_bookings_delete` AFTER DELETE ON `bookings` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'bookings',
    OLD.booking_id,
    'DELETE',
    CONCAT(
      'user_id: ', OLD.user_id, ', ',
      'room_id: ', OLD.room_id, ', ',
      'booking_check_in: ', OLD.booking_check_in, ' ', OLD.booking_checkin_time, ', ',
      'booking_check_out: ', OLD.booking_check_out, ' ', OLD.booking_checkout_time, ', ',
      'booking_total_price: ', OLD.booking_total_price, ', ',
      'booking_status: ', OLD.booking_status
    ),
    NULL,
    @current_user_id  
  );
END$$

DELIMITER ; 