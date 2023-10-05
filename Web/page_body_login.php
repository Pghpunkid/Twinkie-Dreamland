<?php
    //$mcdb = new MCDB($miscreatedDB);
    echo '<div class="main">
        <div class="container">
            <br/>
            <h1 class="uppercase">Login</h1>
            <hr/>
            <p>Login with your Steam account to gain access to extra features!</p>
            <p>All login credential information is handled by Steam, and is not collected by us.</p>
            <br/>
            <div class="row">
                <div class="offset-sm-3 col-sm-6 center">';
/*                    <p>In order to login with Username and Password, you must login with Steam first, and set your password in the Player Portal once logged in.</p>
                    <form>
                        <div class="form-group row">
                            <label for="twdluser" class="col-sm-4 col-form-label left">Username:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="twdluser" value="Username">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword" class="col-sm-4 col-form-label left">Password:</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="inputPassword" placeholder="Password">
                            </div>
                        </div>
                    </form>
                    <a href="#" class="col-12 btn btn-primary">Login</a><br/><br/>';
*/
                    if ($host_name == 'twinkiedreamland') {
                        echo '<a href="/fakelogin"><img src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png"></a>';
                    }
                    else {
                        echo loginbutton('rectangle');
                    }

                    echo '
                </div>
            </div>
        </div>
    </div>
    <br/>';
?>
