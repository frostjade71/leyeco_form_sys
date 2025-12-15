-- Add dispatch details fields to complaints table
-- Migration: Add Dispatch Details
-- Date: 2025-12-15

ALTER TABLE complaints
ADD COLUMN dispatch_to VARCHAR(255) DEFAULT NULL COMMENT 'Dispatcher name (no account needed)',
ADD COLUMN dispatch_mode VARCHAR(50) DEFAULT NULL COMMENT 'Mode: Handcarried, Radio/SMS/Chat/E-mail, Others',
ADD COLUMN dispatch_by INT DEFAULT NULL COMMENT 'Staff ID who dispatched',
ADD COLUMN dispatch_date TIMESTAMP NULL DEFAULT NULL COMMENT 'Date and time of dispatch',
ADD COLUMN action_taken TEXT DEFAULT NULL COMMENT 'Action taken by concerned personnel',
ADD COLUMN acknowledged_by VARCHAR(255) DEFAULT NULL COMMENT 'Person who acknowledged',
ADD COLUMN date_settled TIMESTAMP NULL DEFAULT NULL COMMENT 'Date settled';

-- Add index for dispatch_by for faster lookups
ALTER TABLE complaints
ADD INDEX idx_dispatch_by (dispatch_by);
