<?php
/**
 * LEYECO III Requisition System Configuration
 */

// Departments
define('REQ_DEPARTMENTS', [
    'Technical Services Department',
    'Finance Services Department',
    'Institutional Services Department',
    'Office of the General Manager'
]);

// Approval Levels and Roles
define('REQ_APPROVAL_LEVELS', [
    1 => 'Noted by - Department Head',
    2 => 'Checked by - Warehouse Section Head',
    3 => 'Reviewed by - Budget Officer',
    4 => 'Checked By - Internal Auditor',
    5 => 'Approved By - General Manager'
]);

// Status options
define('REQ_STATUSES', [
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'completed' => 'Completed'
]);

// Units of measurement
define('REQ_UNITS', [
    'pcs',
    'kg',
    'meters',
    'liters',
    'boxes',
    'rolls',
    'sets',
    'pairs',
    'units',
    'bags',
    'bottles',
    'cans',
    'gallons',
    'reams'
]);

// Remarks options for Level 4 approver
define('REQ_REMARKS_OPTIONS', [
    'Approved',
    'For Canvass',
    'For Quotation',
    'For Purchase Order',
    'Stock Available',
    'Out of Stock',
    'Partially Available'
]);