<?php

include_once('_init.php');

$authenticated_user = $_SESSION['user'];

if (isset($_POST['action'])) {

    switch ($_POST['action']) {

        case "COURSE_ADD_STAFF":
            if (!$_POST['course_id'] || !$_POST['user_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            if($authenticated_user['id'] != $_POST['user_id'] &&
                !User::withUserObjectData($_SESSION['user'])->hasPermission(array('Administrator'))) {

                echo "ERROR: Not authorized";
                break;
            }

            $success = addStaff($_POST['course_id'], $_POST['user_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR";
            break;


        case "COURSE_REMOVE_STAFF":
            if (!$_POST['course_id'] || !$_POST['user_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            if($authenticated_user['id'] != $_POST['user_id'] &&
                !User::withUserObjectData($_SESSION['user'])->hasPermission(array('Administrator'))) {

                echo "ERROR: Not authorized";
                break;
            }

            // check deadline
            if($authenticated_user['id'] == $_POST['user_id']) {

                if(!isset($_POST['deadline'])) {
                    echo "ERROR: Deadline not set.";
                    break;
                }
                else {
                    $deadline = intval($_POST['deadline']) - 1;
                    $course = getCourse($_POST['course_id']);

                    $durationString = 'P' . $deadline . 'D';
                    $deadline_date = (new DateTime())->add(new DateInterval($durationString));

                    if($deadline_date > $course['dates'][0]['date']) {
                        echo "ERROR: Du kannst dich nicht mehr austragen. Deadline {$deadline} Tage vor Kursbeginn.";
                        break;
                    }
                }
            }

            $success = removeStaff($_POST['course_id'], $_POST['user_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR";
            break;


        case "USER_ADD_ROLE":
            if(!User::withUserObjectData($_SESSION['user'])->hasPermission(array('Administrator'))) {
                echo "ERROR: Not authorized";
                break;
            }
            if (!$_POST['user_id'] || !$_POST['role_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            $success = addRole($_POST['user_id'], $_POST['role_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR";
            break;


        case "USER_REMOVE_ROLE":
            if(!User::withUserObjectData($_SESSION['user'])->hasPermission(array('Administrator'))) {
                echo "ERROR: Not authorized";
                break;
            }
            if (!$_POST['user_id'] || !$_POST['role_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            $success = removeRole($_POST['user_id'], $_POST['role_id']);

            if ($success) echo "SUCCESS";
            else echo "ERROR";
            break;


        case "USER_DELETE":
            if(!User::withUserObjectData($_SESSION['user'])->hasPermission(array('Administrator'))) {
                echo "ERROR: Not authorized";
                break;
            }
            if (!$_POST['user_id']) {
                echo "ERROR: Parameter missing";
                break;
            }

            $success = deleteItem($_POST['user_id'], "user");

            if ($success) echo "SUCCESS";
            else echo "ERROR";
            break;


        default:
            echo "Unknown.";
            break;
    }
}

?>