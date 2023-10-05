<?php
    echo "
    <div class='main'>
        <div class='container'>
            <br/>
            <h1 class='uppercase'>Change Requests</h1>
            <hr/>
            <div id='change-request-list'>
                <div class='float-right'>
                    <select class='form-control' id='cr-list-filter'>
                        <option value='active' selected>Active</option>
                        <option value='all'>All</option>
                        <option value='canceled'>Canceled</option>
                        <option value='review'>Review</option>
                        <option value='completed'>Completed</option>
                        <option value='in-progress'>In Progress</option>
                        <option value='on-hold'>On Hold</option>
                    </select>
                </div>
                <a href='/new-change-request' class='btn btn-sunray'>Create a Change Request</a><br/><br/>
                <div id='change-requests'>
                    <div class='center'>Loading...</div>
                </div>
            </div>
            <div id='change-request-item' style='display:none;'>
                <button id='change-request-back' class='float-right btn btn-sunray'>Back</button>
                <h4 id='request-short-detail'></h4>
                <div id='requested-by'></div>
                <div id='status'></div>
                <br/>
                <p>Request:</p>
                <div id='request-detail'></div>
                <br/>
                <strong>Notes:</strong>
                <div id='request-notes'></div>
            </div>
        </div>
    </div>
    ";
?>
