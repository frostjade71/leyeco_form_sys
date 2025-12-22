-- Migration script to add last_activity column to existing sessions table
-- Run this script if the sessions table already exists

-- Add last_activity column
ALTER TABLE `sessions` 
ADD COLUMN `last_activity` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `expires_at`;

-- Add index on last_activity for efficient cleanup queries
ALTER TABLE `sessions` 
ADD KEY `idx_last_activity` (`last_activity`);

-- Update existing sessions to have current timestamp as last_activity
UPDATE `sessions` 
SET `last_activity` = NOW() 
WHERE `last_activity` IS NULL;
