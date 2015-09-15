<?php

include_once('_init.php');

$required_roles = array('Administrator');

if(User::withUserObjectData($_SESSION['user'])->hasPermission($required_roles)) {

    $course_types = getCourseTypes();

    if (isset($_GET["id"])) {
        // course details
        $course_id = $_GET["id"];
        $course = getCourse($course_id);
        $registrants = getRegistrants($course_id);

        $title = "Kursdetails";
        $content = "
                <span class='list'>
                    <span class='list-item'>
                        <span>Kurs ID</span><span>{$course_id}</span>
                    </span>
                    <span class='list-item'>
                        <span>Kurstyp</span><span>{$course_types[$course['course_type_id']]}</span>
                    </span>
                    <span class='list-item'>
                        <span>Maximale Teilnehmerzahl</span><span>{$course['max_participants']}</span>
                    </span>
                    <span class='list-item'>
                        <span>Bereits registrierte Teilnehmer</span>
                        <span>" . count($registrants) . " (<a href='{$root_directory}/course/{$course_id}/registrants'>anzeigen</a>)</span>
                    </span>";

        $counter = 1;
        foreach ($course['dates'] as $date) {

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
                </span>
                <a href='{$root_directory}/archive' class='button'>Zurück</a>";
    } else {
        /***********************************************************************/
        /* Course overview                                                     */
        /***********************************************************************/
        $title = "Archiv";

        $first_year = 2015;
        $temp_date = new DateTime();
        $current_year = intval($temp_date->format('Y'));
        $months = array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

        $content .= "
                <div class='archive-filter'>
                    <label for='archive-filter-year'>Wähle das Jahr: </label>
                    <select id='archive-filter-year'>";

        for($i = $first_year; $i<=$current_year; $i++) {
            $content .= "<option value='{$i}'>{$i}</option>";
        }

        $content .= "
                    </select>
                    <label for='archive-filter-month'>Wähle den Monat: </label>
                    <select id='archive-filter-month'>";

        foreach($months as $month) {
            $content .= "<option value='{$month}'>{$month}</option>";
        }

        $content .= "
                    </select>
                </div>
                <div class='list'>
                    <span class='list-heading'>
                        <span>Kurstyp</span>
                        <span>Datum</span>
                        <span class='no-mobile'>Plätze</span>
                        <span class='no-mobile'>Anmeldungen</span>
                        <span></span>
                    </span>";

        $courses = getCourses(true);

        $month = null;
        foreach ($courses as $course) {
            $registrants = getRegistrants($course['id']);
            $num_registrants = count($registrants);

            $month = getMonth($course['date']);

            $content .= "
                    <span class='list-item' year='{$course['date']->format('Y')}' month='{$month}'>
                        <span>{$course_types[$course['course_type_id']]}</span>
                        <span>{$course['date']->format('d.m.Y')}</span>
                        <span class='no-mobile'>{$course['max_participants']}</span>
                        <span class='no-mobile'>$num_registrants (<a href='{$root_directory}/course/{$course['id']}/registrants'>Liste</a>)</span>
                        <span><a href='./archive/{$course['id']}'>Details</a></span>
                    </span>";
        }

        $content .= "
                </div>";
    }
}
else {
    $title = "Archiv";
    $content = "Du hast keine Berechtigung für diesen Bereich der Website.";
}

$content_class = "archive";
include('_main.php');
?>
