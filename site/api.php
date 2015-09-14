<?php

include_once('_init.php');

$required_roles = array('Administrator');

if(User::withUserObjectData($_SESSION['user'])->hasPermission($required_roles)) {

    if (isset($_POST['action'])) {

        switch ($_POST['action']) {

            case "COURSE_ADD_STAFF":
                if (!$_POST['course_id'] || !$_POST['user_id']) {
                    echo "ERROR: Parameters missing";
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

                $success = removeStaff($_POST['course_id'], $_POST['user_id']);

                if ($success) echo "SUCCESS";
                else echo "ERROR";
                break;
            case "USER_ADD_ROLE":
                if (!$_POST['user_id'] || !$_POST['role_id']) {
                    echo "ERROR: Parameters missing";
                    break;
                }

                $success = addRole($_POST['user_id'], $_POST['role_id']);

                if ($success) echo "SUCCESS";
                else echo "ERROR";
                break;
            case "USER_REMOVE_ROLE":
                if (!$_POST['user_id'] || !$_POST['role_id']) {
                    echo "ERROR: Parameters missing";
                    break;
                }

                $success = removeRole($_POST['user_id'], $_POST['role_id']);

                if ($success) echo "SUCCESS";
                else echo "ERROR";
                break;
            case "USER_DELETE":
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
}
else {
    $title = "Warnung";
    $content = "Du hast keine Berechtigung für diesen Bereich der Website.";

    $content_class = "basic";
    include('_main.php');
}
?>