<?php
    echo "
    <div class='main'>
        <div class='container'>
            <br/>
            <div class='jumbotron' id='welcome_panel'>
                <h1 class='uppercase'>It's time to nut up, or shut up!</h1>
                <p class='lead'>Welcome to Woody's Twinkie Dreamland, the Miscreated server that pays homage to Zombieland! Join us, and see just how quickly things can go from 'bad' to 'total shit storm'!</p>
                <a class='btn btn-lg btn-primary' href='#' id='joinGame' role='button'>Play Now</a>
                <a class='btn btn-lg btn-discord' href='https://discordapp.com/invite/5djCeHK' role='button'>Join Our Discord</a>
            </div>
            <h1 class='uppercase'>News</h1>
            <hr/>
            <div id='newsContent'>";


                if (!$token) {
                    $request = json_decode(file_get_contents("https://api.twinkiedreamland.com/api/v1.0/token.php?param=request"),true);
                    if ($request['Status']) {
                        $token = $request['Token'];
                    }
                    else {
                        echo "<p>FAIL1</p>";
                    }
                }

                $request = json_decode(file_get_contents("https://api.twinkiedreamland.com/api/v1.0/blog.php?param=get&token=$token"),true);
                if (!isset($request['Posts'])) {
                    echo "<p>FAIL2</p>";
                }
                else {
                    $posts = $request['Posts'];
                    if (sizeof($posts) > 0) {
                        for ($p=0; $p<sizeof($posts); $p++) {
                            echo "
                            <div class='article'>
                                <h2 class='article_title uppercase'>".$posts[$p]['Title']."</h2>
                                <p>Published <span class='article_date'>".$posts[$p]['EnglishDateTime']."</span> by <span class='article_author'>".$posts[$p]['Name']."</span></p>
                                <hr/>
                                <div class='article_banner'>
                                    ".$posts[$p]['Banner']."
                                </div>
                                <div class='article_body'>
                                    ".$posts[$p]['Body']."
                                </div>
                            </div>
                            ";
                        }
                    }
                    else {
                        echo "
                        <div class='article'>
                            <p class='center'>No articles to show. :(</p>
                        </div>
                        ";
                    }
                }


    echo "
            </div>
        </div>
    </div>
    <br/>
    <div id='joinModal' class='modal' tabindex='-1' role='dialog'>
        <div class='modal-dialog' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title'>Real Quick..</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <p>Just in case the join button does not work below, you can also join our server using the in-game server browser.</p>
                    <p>Navigate to the All tab, and sort the Server by Name descending, and look for Woody's Twinkie Dreamland!</p>
                    <br/>
                    <p>If you need help, join our Discord and we can help you!</p>
                    <div class='center'>
                        <a class='btn btn-lg btn-primary' href='steam://run/299740/connect/+connect 91.242.214.177 64000' role='button'>Join Miscreated Server</a><br/><br/>
                        <a class='btn btn-lg btn-discord' href='https://discordapp.com/invite/5djCeHK' role='button'>Join Our Discord</a>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                </div>
            </div>
        </div>
    </div>
    ";
?>
