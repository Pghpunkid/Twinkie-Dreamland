<?php
    unset($_SESSION['steamid']);
    unset($_SESSION['serverAdminLevel']);

    if ($host_name == "twinkiedreamland") {
        header("Location: /");
    }
    else {
        header("Location: https://www.twinkiedreamland.com");
    }
?>
