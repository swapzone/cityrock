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
            <span>Uhrzeit</span>
            <span>Art</span>
            <span class='no-mobile'>Titel</span>
            <span class='no-mobile'>Übungsleiter</span>
            <span></span>
            <span></span>
        </span>";

    $content = "";

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
        // check if course is within the next 7 days
        if ($course['date'] < $temp_date->add(new DateInterval($duration_string))) {
            $staff = getStaff($course['id']);
            $staff_num = count($staff);

            $user_is_subscribed = false;
            foreach($staff as $user) {
                $userObj = $user->serialize();
                if($userObj['id'] == $_SESSION['user']['id']) $user_is_subscribed = true;
            }

            $staff_is_full = $staff_num >= $course['min_staff'];

            $display_subscribe_button = $user_is_subscribed || $staff_is_full ? "display: none;" : "";
            $display_unsubscribe_button = $user_is_subscribed ? "" : "display: none;";

            if ($course['date']->format('d.m.Y') != $date) {
                $date = $course['date']->format('d.m.Y');
                $content .= "<span class='course-list-month'>{$date}</span>
                {$table_heading}";
            }

            $hours = floor($course['duration'] / 60);
            $minutes = ($course['duration'] / 60 - $hours) * 60;

            $course_duration = 'PT' . $hours . 'H' . $minutes . 'M';

            $course_end_time = clone $course['date'];
            $course_duration_object = new DateInterval($course_duration);
            $course_end_time = $course_end_time->add($course_duration_object);
            $course_end_time = $course_end_time->format('h:i');

            $content .= "
            <span class='list-item $item_class'>
                <span>{$course['date']->format('h:i')} - {$course_end_time} Uhr</span>
                <span>
                    {$course_types[$course['course_type_id']]['title']}
                </span>
                <span class='no-mobile'>{$course['title']}</span>
                <span class='no-mobile'>
                    {$staff_num} / {$course['min_staff']}
                </span>
                <span><a href='{$root_directory}/events/{$course['id']}'>Details</a></span>
                <span>
                    <a user-id='{$_SESSION['user']['id']}' event-id='{$course['id']}' class='event-subscribe' style='{$display_subscribe_button}'>Eintragen</a>
                    <a deadline='{$config['system']['staff-cancel-deadline ']}' user-id='{$_SESSION['user']['id']}' event-id='{$course['id']}' class='event-unsubscribe' style='{$display_unsubscribe_button}'>Austragen</a>
                </span>
            </span>";
        }
    }

    $content .= "
    </div>"; 
}

$content_class = "course";
include('_main.php');
?>
