<?php
// The current schema has authentication but no user role or access level.
function check_access($level = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['USER_ID']) || (int) $_SESSION['USER_ID'] < 1) {
        return false;
    }

    return true;
}
?>