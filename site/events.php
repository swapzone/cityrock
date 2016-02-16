<?php

include_once('_init.php');

$authenticated_user = $_SESSION['user'];
$authenticated_user_object = User::withUserObjectData($_SESSION['user']);

$title = "Veranstaltungen";
$content = "Noch nicht fertig.";

$number_of_days = 14;
$course_types = getCourseTypes();

// include config_lite library
require_once('lib/config/Lite.php');
$config = new Config_Lite('basic.cfg');

if(isset($_GET["id"])) {
    /***********************************************************************/
    /* Event details                                                       */
    /***********************************************************************/
    $course_id = $_GET["id"];
    $course = getCourse($course_id);

    $staff = getStaff($course_id);
    $staff_num = count($staff);

    $registrants = getRegistrants($course_id);
    $user_is_subscribed = false;

    $staff_list = "<span class='staff-list'>";
    foreach ($staff as $user) {
        $userObj = $user->serialize();
        $staff_list .= "<span>{$userObj['first_name']} {$userObj['last_name']}</span>";

        if($userObj['id'] == $_SESSION['user']['id']) $user_is_subscribed = true;
    }
    $staff_list .= "</span>";

    $staff_is_full = $staff_num >= $course['min_staff'];

    $display_subscribe_button = $user_is_subscribed || $staff_is_full ? "display: none;" : "";
    $display_unsubscribe_button = $user_is_subscribed ? "" : "display: none;";

    $title = "Kursdetails";
    $content = "
        <span class='list'>
            <span class='list-item' style='display: none;'>
                <span style='display: none;'>Kurs ID</span>
                <span style='display: none;' id='course-id'>{$course_id}</span>
            </span>
            <span class='list-item'>
                <span>Kunde/Titel</span><span>{$course['title']}</span>
            </span>
            <span class='list-item'>
                <span>Kurstyp</span>
                <span>{$course_types[$course['course_type_id']]['title']}</span>
            </span>
            <span class='list-item'>
                <span>Anzahl Teilnehmer</span>
                <span>" . count($registrants) ." / {$course['max_participants']}</span>
            </span>
            <span class='list-item'>
                <span>Alter der Teilnehmer</span>
                <span>{$course['participants_age']}</span>
            </span>
            <span class='list-item'>
                <span>Übungsleiter</span>
                <span>
                    {$staff_num} / {$course['min_staff']}
                    {$staff_list}
                </span>
            </span>";

    $counter = 1;
    foreach($course['dates'] as $date) {

        $content .= "
            <span class='list-item'>
                <span>Datum (Tag $counter)</span>
                <span>{$date['date']->format('d.m.Y')}</span>
            </span>
            <span class='list-item'>
                <span>Uhrzeit (Tag $counter)</span>
                <span>{$date['date']->format('h:i')} - " . getEndTime($date['date'], $date['duration']) . " Uhr</span>
            </span>";

        $counter++;
    }

    $content .= "
            <span class='list-item'>
                <span>Addresse</span><span>{$course['street']}, {$course['plz']} {$course['city']}</span>
            </span>
        </span>
        <a href='{$root_directory}/events' class='button'>Übersicht</a>";

    if($authenticated_user_object->hasPermission(array('Administrator'))) {
        $content .= "<a href='{$root_directory}/course/{$course_id}/edit' class='button'>Bearbeiten</a>";
    }

    $content .= "
        <span><a user-id='{$_SESSION['user']['id']}' event-id='{$course_id}' class='event-subscribe button' style='{$display_subscribe_button}'>Eintragen</a></span>
        <span><a deadline='{$config['system']['staff-cancel-deadline ']}' user-id='{$_SESSION['user']['id']}' event-id='{$course_id}' class='event-unsubscribe button' style='{$display_unsubscribe_button}'>Austragen</a></span>";
}
else {
    /***********************************************************************/
    /* Event overview                                                      */
    /***********************************************************************/
    $title = "Veranstaltungen";

    $table_heading = "
        <span class='list-heading'>
            <span class='title-col'>Art</span>
            <span>Uhrzeit</span>
            <span class='staff-col' class='no-mobile'>Übungsleiter</span>
            <span></span>
            <span></span>
        </span>";

    $user_id = $authenticated_user['id'];

    $all_active = $_GET['filter'] === 'all' || !isset($_GET['filter']) ? 'active' : '';
    $user_active = $_GET['filter'] === 'user' ? 'active' : '';
    $open_active = $_GET['filter'] === 'open' ? 'active' : '';

    $content = "
    <div id='event-filter' class='filter'>
        <span event-type='all' class='all {$all_active}'>Alle Termine</span>
        <span event-type='user' class='{$user_active}'>Meine Termine</span>
        <span event-type='open' class='{$open_active}'>Offene Termine</span>
    </div>";

    $date_object = new DateTime();
    $date = $date_object->format('d.m.Y');

    $duration_string = 'P' . $number_of_days . 'D';
    $end_date = clone $date_object;
    $end_date->add(new DateInterval($duration_string));

    $courses = getCourses(false, null, new DateTime(), $end_date);

    $cleaned_up_events = removePastDates($courses, $date_object);
    $repeating_events = createIntervalDates($courses, $date_object, $end_date);

    $merged_events = array_merge($cleaned_up_events, $repeating_events);
    $all_events = removeDateExceptions($merged_events);

    $temp_date = new DateTime();

    foreach ($all_events as $course) {
        $staff = getStaff($course['id']);
        $staff_num = count($staff);
        $staff_is_full = $staff_num >= $course['min_staff'];

        $user_list = "";

        $user_is_subscribed = false;
        foreach($staff as $user) {
            $userObj = $user->serialize();

            $user_list .= $userObj['first_name'] . ' ' . $userObj['last_name'] . '<br />';

            if($userObj['id'] == $authenticated_user['id']) $user_is_subscribed = true;
        }

        // check if couse shall be shown (active property of course type)
        if(intval($course_types[$course['course_type_id']]['active']) === 0)
            continue;

        // check if course is within the next 7 days
        if ($course['date'] > $temp_date->add(new DateInterval($duration_string))) 
            continue;

        // check if authenticated user is registered for this course
        if($user_active && !$user_is_subscribed)
            continue; 

        // check if course has missing staff
        if($open_active && $staff_is_full)
            continue;

        $display_subscribe_button = $user_is_subscribed || $staff_is_full ? "display: none;" : "";
        $display_unsubscribe_button = $user_is_subscribed ? "" : "display: none;";

        $day_color = date('N', strtotime($date)) >= 6 ? '#990000' : '';

        if ($course['date']->format('d.m.Y') != $date) {
            $date = $course['date']->format('l, d.m.Y');
            $date = strtr($date, $day_translations);

            $content .= "<span class='course-list-month' style='color: {$day_color};'>{$date}</span>
            {$table_heading}";
        }

        $hours = floor($course['duration'] / 60);
        $minutes = ($course['duration'] / 60 - $hours) * 60;

        $course_duration = 'PT' . $hours . 'H' . $minutes . 'M';

        $course_end_time = clone $course['date'];
        $course_duration_object = new DateInterval($course_duration);
        $course_end_time = $course_end_time->add($course_duration_object);
        $course_end_time = $course_end_time->format('h:i');

        $course_type_title = $course_types[$course['course_type_id']]['title'];
        $course_type_color = $course_types[$course['course_type_id']]['color'];

        $status_color = $staff_is_full ? 'green' : 'red';

        $content .= "
        <span class='list-item $item_class'>
            <span class='title-col' style='vertical-align: top;'>
                <span style='display: inline-block; width: 1em; height: 1em; margin-right: 0.2em; background-color: {$course_type_color}'></span>
                <span style='vertical-align: top;'>{$course_type_title}</span>
            </span>
            <span style='vertical-align: top;'>{$course['date']->format('h:i')} - {$course_end_time} Uhr</span>
            <span class='no-mobile staff-col' style='vertical-align: top;'>
                <div style='float: left; font-size: 0.9em; color: {$status_color}'>{$staff_num}/{$course['min_staff']}</div>
                <div style='float: left; margin-left: 1.5em; font-size: 0.9em;'>{$user_list}</div>
            </span>
            <span style='vertical-align: top;'><a href='{$root_directory}/events/{$course['id']}'>Details</a></span>
            <span style='vertical-align: top;'>
                <a user-id='{$_SESSION['user']['id']}' event-id='{$course['id']}' class='event-subscribe' style='{$display_subscribe_button}'>Eintragen</a>
                <a deadline='{$config['system']['staff-cancel-deadline ']}' user-id='{$_SESSION['user']['id']}' event-id='{$course['id']}' class='event-unsubscribe' style='{$display_unsubscribe_button}'>Austragen</a>
            </span>
        </span>";
    }

    $content .= "
    </div>"; 
}

$content_class = "course";
include('_main.php');
?>
