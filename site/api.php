<?php

include_once('_init.php');

if(isset($_POST['action'])) {

    switch($_POST['action']) {

        case "COURSE_ADD_STAFF":
            if(!$_POST['course_id'] || !$_POST['user_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            $success = addStaff($_POST['course_id'], $_POST['user_id']);

            if($success) echo "SUCCESS";
            else echo "ERROR";
            break;
        case "COURSE_REMOVE_STAFF":
            if(!$_POST['course_id'] || !$_POST['user_id']) {
                echo "ERROR: Parameters missing";
                break;
            }

            $success = removeStaff($_POST['course_id'], $_POST['user_id']);

            if($success) echo "SUCCESS";
            else echo "ERROR";
            break;
        default:
            echo "Unknown.";
            break;
    }
}

?>