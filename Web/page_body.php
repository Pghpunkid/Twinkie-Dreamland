<?php

    if ($page_name == '/' || $page_name == '/index' || $page_name == '/index.php') {
        include("page_body_index.php");
    }
    else if ($page_name == '/changelog' || $page_name == '/changelog.php') {
        include("page_body_changelog.php");
    }
    else if ($page_name == '/change-request' || $page_name == '/change-request.php') {
        if (!$loggedIn) {
            header("Location: /login");
        }
        include("page_body_change_request.php");
    }
    else if ($page_name == '/new-change-request' || $page_name == '/new-change-request.php') {
        include("page_body_new_change_request.php");
    }
    else if ($page_name == '/mod-list' || $page_name == '/mod-list.php') {
        include("page_body_mod_list.php");
    }
    else if ($page_name == '/server-rules' || $page_name == '/server-rules.php') {
        include("page_body_server_rules.php");
    }
    else if ($page_name == '/survival-guide' || $page_name == '/survival-guide.php') {
        include("page_body_survivalguide.php");
    }
	else if ($page_name == '/map' || $page_name == '/map.php') {
        include("page_body_map.php");
    }
    else if ($page_name == '/player-portal' || $page_name == '/player-portal.php') {
        if (!$loggedIn) {
            header("Location: /login");
        }
        include("page_body_playerportal.php");
    }
    else if ($page_name == '/admin-portal' || $page_name == '/admin-portal.php') {
        if (!$loggedIn) {
            header("Location: /login");
        }
        else if ($serverAdminLevel < $SERVER_ADMIN_LEVEL) {
            header("Location: /");
        }
        include("page_body_adminportal.php");
    }
    else if ($page_name == '/tile-test' || $page_name == '/tile-test.php') {
        include("page_body_tiletest.php");
    }
    else if ($page_name == '/login' || $page_name == '/login.php') {
        include("page_body_login.php");
    }
    else if ($page_name == '/logout' || $page_name == '/logout.php') {
        include("page_body_logout.php");
    }
    else if ($page_name == '/fakelogin' || $page_name == '/fakelogin.php') {
        include("page_body_fakelogin.php");
    }
    else if ($page_name == '/realtime' || $page_name == '/realtime.php') {
        if (!$loggedIn) {
            header("Location: /login");
        }
        else if ($serverAdminLevel < $SERVER_DEVELOPER_LEVEL) {
            header("Location: /");
        }
        include("page_body_realtime.php");
    }

    echo '
    <!-- Modal -->
    <div class="modal fade" id="playerlist-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" id="playerlist-modal-header">
                    <h5 class="modal-title uppercase" id="playerlist-modal-title">Players Online</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="playerlist-modal-body">
                </div>
            </div>
        </div>
    </div>
    ';
?>
