<?php
    echo "
    <div class='main'>
        <div class='container'>
            <br/>
            <h1 class='uppercase'>Change Requests</h1>
            <hr/>
            <div>
            <form id='cr-form'>
                <p>This information will be published on the website! Do not enter any information that you do not want to be visible on the website!</p>
                <p>Please check out the existing <a href='/change-request' class='btn btn-sm btn-sunray'>Change Requests<a/> before entering one to help prevent duplicates! Thank you!</p>
                <div class='form-group'>
                    <label for='request-type'>What type of request do you have?</label>
                    <select id='request-type' class='form-control'>
                    </select>
                </div>
                <div class='form-group'>
                    <label for='request-item'>Specific item related to request:</label>
                    <select id='request-item' class='form-control'>
                    </select>
                </div>
                <div class='form-group'>
                    <label for='request-short-detail'>Short description of your request:</label>
                    <input id='request-short-detail' class='form-control' />
                </div>
                <div class='form-group'>
                    <label for='request-detail'>Request Details (Explain your request):</label>
                    <textarea rows='4' id='request-detail' class='form-control'></textarea>
                </div>
                <div class='form-group'>
                    <label for='request-author'>Name/Discord ID</label>
                    <input id='request-author' class='form-control'/>
                </div>
                <div class='float-right'><br/><button id='cr-submit' class='btn btn-sunray'>Submit</button></div>
                <div id='captcha'></div>
            </div>
        </div>
    </div>
    ";
?>
