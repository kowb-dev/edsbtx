<?php
// Configuration for EDS Individual Solutions
// Create IBlock for leads first, then update this ID

define('EDS_LEADS_IBLOCK_ID', 50); // Update with actual IBlock ID
define('EDS_CONSULTATION_IBLOCK_ID', 50); // Or separate IBlock
define('EDS_UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('EDS_UPLOAD_DIR', '/individualnye-resheniya/uploads/');
define('EDS_MANAGER_EMAIL', 'orders@edsy.ru');
define('EDS_AUTO_ASSIGN_MANAGER', true);
define('EDS_DEBUG_MODE', true); // Set to false in production
?>