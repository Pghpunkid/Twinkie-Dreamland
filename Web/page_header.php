<?php
    echo '<header>
        <nav class="navbar navbar-expand-md navbar-dark '.($isDev?'bg-blue':'bg-sunray').'">
            <div class="container">
                <a class="navbar-brand uppercase" href="/"><img id="nav-brand-img" src="images/Twinkie Dreamland - 100x150.png" /></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item'.($page_name == '/index' || $page_name == 'index' || $page_name == '' || $page_name == '/'?' active':'').'">
                            <a class="nav-link" href="/">News</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Information
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/changelog">Change Log</a>
                                <a class="dropdown-item" href="/survival-guide">Survival Guide</a>
                                <a class="dropdown-item" href="/server-rules">Server Rules</a>
                                <a class="dropdown-item" href="/mod-list">Mod List</a>
                            </div>
                        </li>
                        <li class="nav-item'.($page_name == '/new-change-request' || $page_name == '/new-change-request.php' || $page_name == '/change-request' || $page_name == '/change-request.php'?' active':'').'">
                            <a class="nav-link" href="/new-change-request">Change Request</a>
                        </li>
                        <li class="nav-item'.($page_name == '/map' || $page_name == '/map.php'?' active':'').'">
                            <a class="nav-link" href="/map">Map</a>
                        </li>'
                        .
                        ($loggedIn?
                        '<li class="nav-item'.($page_name == '/player-portal' || $page_name == '/player-portal.php'?' active':'').'">
                            <a class="nav-link" href="/player-portal">Player Portal</a>
                        </li>':'')
                        .
                        ($serverAdminLevel >= $SERVER_ADMIN_LEVEL?
                        '<li class="nav-item'.($page_name == '/admin-portal' || $page_name == '/admin-portal.php'?' active':'').'">
                            <a class="nav-link" href="/admin-portal">Admin Portal</a>
                        </li>':'')
                        .
                        ($serverAdminLevel >= $SERVER_DEVELOPER_LEVEL?
                        '<li class="nav-item'.($page_name == '/realtime' || $page_name == '/realtime.php'?' active':'').'">
                            <a class="nav-link" href="/realtime">Realtime Map</a>
                        </li>':'')
                        .
                        ($loggedIn?
                        '<li class="nav-item">
                            <a class="nav-link" href="/logout">Logout</a>
                        </li>':'<li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>')
                        .
                        '
                    </ul>
                    <div id="wcContainer" class="form-inline my-2 my-lg-0">
                        <div id="weather-center">
                            <table width="100%">
                                <tr>
                                    <td colspan="2" class="center" id="weather-icon-parent">
                                        <img id="weather-icon" class="weather-icon" src="images/loading.gif" width="100%"/><br/>
                                        <span id="weather-description">--</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="center"><span class="weather-field" id="time">--</span></td>
                                    <td class="center"><a href="#" id="player-list-info"><span class="weather-field" id="current-players">--</span>/<span class="weather-field" id="max-players">--</span></a></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="center">
                                        <div class="restart-scroll"><div id="next-restart"></div></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="container">
        <br/>
        <div id="weather-alert" class="alert alert-danger-custom" style="display:none;"></div>
        <audio id="weather-alert-siren" preload="auto">
            <source src="audio/Siren.mp3" type="audio/mpeg">
        </audio>
        <audio id="weather-alert-siren-acidrain" preload="auto">
            <source src="audio/OIWS-AcidRain-FX.mp3" type="audio/mpeg">
        </audio>
        <audio id="weather-alert-siren-nuclearfreeze" preload="auto">
            <source src="audio/OIWS-NuclearFreeze-FX.mp3" type="audio/mpeg">
        </audio>
        <audio id="weather-alert-siren-radstorm" preload="auto">
            <source src="audio/OIWS-RadStorm-FX.mp3" type="audio/mpeg">
        </audio>
        <audio id="weather-alert-siren-tornado" preload="auto">
            <source src="audio/OIWS-Tornado-FX.mp3" type="audio/mpeg">
        </audio>
        <audio id="weather-alert-clear" preload="auto">
            <source src="audio/Clear.mp3" type="audio/mpeg">
        </audio>
    </div>
    ';
?>
