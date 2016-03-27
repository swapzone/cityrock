<?php

include_once('_init.php');

$authenticated_user = $_SESSION['user'];
$authenticated_user_object = User::withUserObjectData($_SESSION['user']);

if (isset($_POST['action'])) {

    switch ($_POST['action']) {

        case "COURSE_ADD_STAFF":
            if (!$_POST['course_id'] || !$_POST['user_id']) {
                echo "ERROR: Parameter fehlen.";
                break;
            }

            if($authenticated_user['id'] != $_POST['user_id'] &&
                !$authenticated_user_object->hasPermission(array('Administrator'))) {

                echo "ERROR: Nicht authorisiert.";
                break;
            }

            if(!userIsAuthorizedForCourse($_POST['user_id'], $_POST['course_id'])) {

                echo "ERROR: Du bist für diesen Veranstaltungstypen nicht freigeschalten.";
                break;
            }

            $success = addStaff($_POST['course_id'], $_POST['user_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;


        case "COURSE_REMOVE_STAFF":
            if (!$_POST['course_id'] || !$_POST['user_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            if($authenticated_user['id'] != $_POST['user_id'] &&
                !$authenticated_user_object->hasPermission(array('Administrator'))) {

                echo "ERROR: Nicht authorisiert.";
                break;
            }

            // check deadline
            if($authenticated_user['id'] == $_POST['user_id'] &&
                !$authenticated_user_object->hasPermission(array('Administrator'))) {

                if(!isset($_POST['deadline'])) {
                    echo "ERROR: Deadline nicht gesetzt.";
                    break;
                }
                else {
                    $deadline = intval($_POST['deadline']) - 1;
                    $course = getCourse($_POST['course_id']);

                    $durationString = 'P' . $deadline . 'D';
                    $temp_date = new DateTime();
                    $deadline_date = $temp_date->add(new DateInterval($durationString));

                    if($course['day_interval'] == 0 && $course['month_interval'] == 0) {

                        if($deadline_date > $course['dates'][0]['date']) {
                            echo "ERROR: Du kannst dich nicht mehr austragen. Deadline {$deadline} Tage vor Kursbeginn.";
                            break;
                        }

                        if(!$course['staff_cancel']) {
                            echo "ERROR: Du kannst dich nicht austragen. Bitte kontaktiere den Kursverantwortlichen.";
                            break;
                        }
                    }
                }
            }

            $success = removeStaff($_POST['course_id'], $_POST['user_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;

        case "COURSE_ADD_EXCEPTION":
            if (!$_POST['course_id'] || !$_POST['date'] || !$_POST['user_id']) {
                echo "ERROR: Parameter fehlen.";
                break;
            }

            if($authenticated_user['id'] != $_POST['user_id'] &&
                !$authenticated_user_object->hasPermission(array('Administrator'))) {

                echo "ERROR: Nicht authorisiert.";
                break;
            }

            $cancellation = isset($_POST['cancellation']) && $_POST['cancellation'] == 1;
            $success = addCourseException($_POST['course_id'], $_POST['date'], $cancellation);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;

        case "USER_ADD_ROLE":
            if(!$authenticated_user_object->hasPermission(array('Administrator'))) {
                echo "ERROR: Nicht authorisiert.";
                break;
            }
            if (!$_POST['user_id'] || !$_POST['role_id']) {
                echo "ERROR: Parameter fehlen.";
                break;
            }

            $success = addRole($_POST['user_id'], $_POST['role_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;


        case "USER_REMOVE_ROLE":
            if(!$authenticated_user_object->hasPermission(array('Administrator'))) {
                echo "ERROR: Nicht authorisiert.";
                break;
            }
            if (!$_POST['user_id'] || !$_POST['role_id']) {
                echo "ERROR: Parameter fehlen.";
                break;
            }

            $success = removeRole($_POST['user_id'], $_POST['role_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;

        case "USER_ADD_EVENT":
            if(!$authenticated_user_object->hasPermission(array('Administrator'))) {
                echo "ERROR: Nicht authorisiert.";
                break;
            }
            if (!$_POST['user_id'] || !$_POST['event_id']) {
                echo "ERROR: Parameter fehlen.";
                break;
            }

            $success = addEventToWhitelist($_POST['user_id'], $_POST['event_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;


        case "USER_REMOVE_EVENT":
            if(!$authenticated_user_object->hasPermission(array('Administrator'))) {
                echo "ERROR: Nicht authorisiert.";
                break;
            }
            if (!$_POST['user_id'] || !$_POST['event_id']) {
                echo "ERROR: Parameter fehlen.";
                break;
            }

            $success = removeEventFromWhitelist($_POST['user_id'], $_POST['event_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;

        case "USER_DELETE":
            if(!$authenticated_user_object->hasPermission(array('Administrator'))) {
                echo "ERROR: Nicht authorisiert.";
                break;
            }
            if (!$_POST['user_id']) {
                echo "ERROR: Parameter fehlt.";
                break;
            }

            $success = deleteItem($_POST['user_id'], "user");

            if ($success) echo "SUCCESS";
            else echo "ERROR: Datenbank Fehler.";
            break;

        default:
            echo "Unknown.";
            break;
    }
}

?>