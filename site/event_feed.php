<?php

include_once('_init.php');

if(!(isset($_POST['start']) && isset($_POST['end']) && isset($_POST['event_type']))) {
    // return empty event list
    echo "[]";
    return;
}

$start_date = DateTime::createFromFormat('Y-m-d', $_POST['start']);
$end_date = DateTime::createFromFormat('Y-m-d', $_POST['end']);

$event_type = $_POST['event_type'];
$user_id = $_POST['user_id'];

$course_types = getCourseTypes();
$courses = getCourses(false, null, $start_date, $end_date);

$cleaned_up_events = removePastDates($courses, $start_date);
$repeating_events = createIntervalDates($courses, $start_date, $end_date);

$all_events = array_merge($cleaned_up_events, $repeating_events);

$jsonString = '[';
foreach($all_events as $event) {

    $staff = explode(",", $event['staff_id']);

    if($event_type == 'all' || $event_type == 'user' && in_array($user_id, $staff) || $event_type == 'open' && count($staff) < $event['min_staff']) {
        $event_start = $event['date']->format('Y-m-d H:i:s');

        $event_end_date = $event['date']->add(new DateInterval('PT' . $event['duration'] . 'M'));
        $event_end = $event_end_date->format('Y-m-d H:i:s');

        $event_color = $course_types[$event['course_type_id']]['color'];

        $jsonString .= '{
            "id": ' . $event['id'] . ',
            "title": "' . $course_types[$event['course_type_id']]['title'] . '",
            "start": "' . $event_start . '",
            "end": "' . $event_end . '",
            "staff": "' . $event['staff_id'] . '",
            "url": "' . $root_directory . '/events/' . $event['id'] . '",
            "color": "' . $event_color . '",
            "textColor" : "#000"
        },';
    }
}

if(strlen($jsonString) > 1)
    $jsonString = substr($jsonString, 0, strlen($jsonString)-1);

$jsonString .= ']';

echo $jsonString;
?>