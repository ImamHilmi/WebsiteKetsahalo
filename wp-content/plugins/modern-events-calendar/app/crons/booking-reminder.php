<?php
/**
 *  WordPress initializing
 */
function mec_find_wordpress_base_path()
{
    $dir = dirname(__FILE__);
    
    do
    {
        if(file_exists($dir.'/wp-config.php')) return $dir;
    }
    while($dir = realpath($dir.'/..'));
    
    return NULL;
}

define('BASE_PATH', mec_find_wordpress_base_path().'/');
if(!defined('WP_USE_THEMES')) define('WP_USE_THEMES', false);

global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
require BASE_PATH.'wp-load.php';

/** @var $main MEC_main **/

// MEC libraries
$main = MEC::getInstance('app.libraries.main');

// Blogs
$blogs = array(1);

// Current Blog ID
$multisite = (function_exists('is_multisite') and is_multisite());
$current_blog_id = get_current_blog_id();

// Multisite
if($multisite)
{
    $db = $main->getDB();
    $blogs = $db->select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
}

$sent_reminders = 0;
$now = current_time('Y-m-d H:i');

foreach($blogs as $blog)
{
    // Switch to Blog
    if($multisite) switch_to_blog($blog);

    // MEC notifications
    $notifications = $main->get_notifications();

    // MEC Settings
    $settings = $main->get_settings();

    // Booking is disabled
    if(!isset($settings['booking_status']) or (isset($settings['booking_status']) and !$settings['booking_status'])) continue;

    $hours = isset($notifications['booking_reminder']['hours']) ? explode(',', trim($notifications['booking_reminder']['hours'], ', ')) : array();

    // Hours are invalid
    if(!is_array($hours) or (is_array($hours) and !count($hours))) continue;

    // Check Last Run Date & Time
    $latest_run = get_option('mec_booking_reminder_last_run_datetime', NULL);
    if($latest_run and strtotime($latest_run) > strtotime('-1 Hour', strtotime($now))) continue;

    /**
     * Notification Sender Library
     * @var $notif MEC_notifications
     */
    $notif = $main->getNotifications();

    foreach($hours as $hour)
    {
        $hour = (int) trim($hour, ', ');

        // Hour is not accepted as a valid value for hours
        if($hour <= 0) continue;

        // It's time of the hour that we're going to check
        $time = strtotime('+'.$hour.' hours', strtotime($now));

        $q = new WP_Query();
        $bookings = $q->query(array
        (
            'post_type'=>$main->get_book_post_type(),
            'posts_per_page'=>-1,
            'post_status'=>'any',
            'meta_query'=>array
            (
                array(
                    'key'=>'mec_confirmed',
                    'value'=>1,
                ),
                array(
                    'key'=>'mec_verified',
                    'value'=>1,
                ),
            ),
            'year'=>date('Y', $time),
            'monthnum'=>date('n', $time),
            'day'=>date('j', $time),
            'hour'=>date('G', $time),
        ));

        // No booking found for this date so proceed to next date
        if(!count($bookings)) continue;

        foreach($bookings as $booking)
        {
            $result = $notif->booking_reminder($booking->ID);
            if($result) $sent_reminders++;
        }
    }

    // Last Run
    update_option('mec_booking_reminder_last_run_datetime', $now, false);
}

// Switch to Current Blog
if($multisite) switch_to_blog($current_blog_id);

echo sprintf(__('%s reminder(s) sent.', 'mec'), $sent_reminders);
exit;