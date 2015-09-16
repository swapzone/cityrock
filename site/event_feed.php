<?php

include_once('_init.php');

if(!($_GET['start'] && $_GET['end'])) {
    // return empty event list
    echo "[]";
    return;
}

$startDate = DateTime::createFromFormat('Y-m-d', $_GET['start']);
$endDate = DateTime::createFromFormat('Y-m-d', $_GET['end']);

$course_types = getCourseTypes();
$events = getCourses(false, null, $startDate, $endDate);

$jsonString = '[';

foreach($events as $event) {

    $eventStart = $event['date']->format('Y-m-d H:i:s');

    $eventEndDate = $event['date']->add(new DateInterval('PT'. $event['duration'] .'M'));
    $eventEnd = $eventEndDate->format('Y-m-d H:i:s');

    // TODO include further event details in here

    $jsonString .= '{
        "id": ' . $event['id'] . ',
        "title": "' . $course_types[$event['course_type_id']] . '",
        "start": "' . $eventStart . '",
        "end": "' . $eventEnd . '",
        "url": "'. $root_directory . '/course/' . $event['id'] .'"
    }';
}

$jsonString .= ']';

echo $jsonString;

?>