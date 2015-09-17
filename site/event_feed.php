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
$events = getCourses(false, null, $start_date, $end_date);

$jsonString = '[';

foreach($events as $event) {

    $staff = explode(",", $event['staff_id']);

    if($event_type == 'all' || $event_type == 'user' && in_array($user_id, $staff) || $event_type == 'open' && count($staff) < $event['min_staff']) {
        $event_start = $event['date']->format('Y-m-d H:i:s');

        $event_end_date = $event['date']->add(new DateInterval('PT' . $event['duration'] . 'M'));
        $event_end = $event_end_date->format('Y-m-d H:i:s');

        // TODO include further event details in here

        $jsonString .= '{
            "id": ' . $event['id'] . ',
            "title": "' . $course_types[$event['course_type_id']] . '",
            "start": "' . $event_start . '",
            "end": "' . $event_end . '",
            "staff": "' . $event['staff_id'] . '",
            "url": "' . $root_directory . '/course/' . $event['id'] . ' "
        },';
    }
}

if(strlen($jsonString) > 1)
    $jsonString = substr($jsonString, 0, strlen($jsonString)-1);

$jsonString .= ']';

echo $jsonString;

?>