var requests = [];
var activeRequestID = false;

$(function() {
    console.log("Ready");

    fetchChangeRequests();

    $(document).on('change', '#cr-list-filter', function(e) {
        e.preventDefault();
        fetchChangeRequests();
    });

    $(document).on('click', '#change-request-back', function(e) {
        $('#change-request-list').show();
        $('#change-request-item').hide();
        activeRequestID = false;
    })

    $(document).on('click', '.request', function(e) {
        e.preventDefault();
        viewChangeRequest($(this).attr('data-request-id'));
    });
});

function fetchChangeRequests() {
    $.post(
        'change-requests-async.php',
        {
            'cmd': 'fetchAll',
            'filter': $('#cr-list-filter').val()
        },
        function(data) {
            if (data.status == "Ok") {

                var html = '<table class="table table-light" id="cr-table"><thead><tr><th class="col-sm-5">Type</th><th class="col-sm-2">Author</th><th class="col-sm-3">Request</th><th class="col-sm-4">Status</th></tr></thead><tbody>';
                requests = data.requests;
                if (requests.length != 0) {
                    for (var r=0; r<requests.length; r++) {
                        var request = requests[r];
                        var status = request.Status;
                        if (request.Completed == 'Y') {
                            status += " - "+request.CompletedVersion;
                        }
                        html += '<tr data-request-id="'+r+'" class="request"><td>'+request.RequestType+" > "+request.RequestItem+"</td><td>"+request.Requestor+"</td><td>"+request.RequestShortDescription+"</td><td>"+status+"</td></tr>";
                    }
                }
                else {
                    html += '<tr><td class="center" colspan="4">No Change Requests To Show</td></tr>';
                }

                html += "</tbody></table>";
                $('#change-requests').html(html);
            }
            else {
                alert(data.response);
            }
        },
        'json'
    );
}

function viewChangeRequest(id) {
    activeRequestID = id;
    var request = requests[activeRequestID];
    $('#request-short-detail').html(request.RequestShortDescription);
    var requested = 'Submitted by '+request.Requestor+' on '+request.RequestDateTimeEng;
    if (request.Completed == 'Y') {
        requested += "<br/>Completed on "+request.CompletedDateTimeEng;
    }
    else if (request.Status == 'Canceled') {
        requested += "<br/>Canceled on "+request.CompletedDateTimeEng;
    }

    $('#requested-by').html(requested);
    var status = '';

    if (request.Status == 'Cancelled') {
        status = '<span class="badge badge-danger">'+request.Status+'</span>';
    }
    else if (request.Status == 'On Hold') {
        status = '<span class="badge badge-warning">'+request.Status+'</span>';
    }
    else if (request.Status == 'Complete') {
        status = '<span class="badge badge-success">'+request.Status+'</span>';
    }
    else if (request.Status == 'Initial') {
        status = '<span class="badge badge-secondary">'+request.Status+'</span>';
    }
    else if (request.Status == 'In Progress') {
        status = '<span class="badge badge-primary">'+request.Status+'</span>';
    }
    else if (request.Status == 'Review') {
        status = '<span class="badge badge-primary">'+request.Status+'</span>';
    }

    $('#status').html(status);
    $('#request-detail').html(request.RequestDescription);

    $('#change-request-list').hide();
    $('#change-request-item').show();

    //Notes
    var notes = "";
    if (request.Notes.length > 0) {
        for (var n=0; n<request.Notes.length; n++) {
            var note = request.Notes[n];
            notes += "<hr/><div class='row'><div class='col-sm-2'>"+note.NoteAuthor+"<br/>"+note.NoteDateTimeEng+"</div><div class='col-sm-10'>"+note.NoteDescription+"</div></div>";
        }
    }
    else {
        notes = "<div class='center'>No Notes</div>";
    }
    notes+="<br/>";
    $('#request-notes').html(notes);
}
