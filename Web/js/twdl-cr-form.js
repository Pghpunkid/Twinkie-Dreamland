const requestTypes = [
    {desc: "Mod Creation/Change",                               value:"Mod Creation/Change"},
    {desc: "Bug Fix",                                           value:"Bug Fix"},
    {desc: "Web Site Change/Update",                            value:"Web Site Change/Update"}
];

const requestItems = [
    {desc: "Primary Weapon (Firearm)",                          value:"Primary Weapon (Firearm)"},
    {desc: "Primary Weapon (Melee)",                            value:"Primary Weapon (Melee)"},
    {desc: "Secondary Weapon (Firearm)",                        value:"Secondary Weapon (Firearm)"},
    {desc: "Secondary Weapon (Melee)",                          value:"Secondary Weapon (Melee)"},
    {desc: "Vehicle",                                           value:"Vehicle"},
    {desc: "Base Building (Size)",                              value:"Base Building (Size)"},
    {desc: "Base Building (Materials/Equipment/Decorations)",   value:"Base Building (Materials/Equipment/Decorations)"},
    {desc: "Food Or Drink Items",                               value:"Food Or Drink Items"},
    {desc: "Medical Items",                                     value:"Medical items"},
    {desc: "A.I. (Zombies/Animals)",                            value:"A.I. (Zombies/Animals)"},
    {desc: "Weather/Time Cycles",                               value:"Weather/Time Cycles"},
    {desc: "Clothing Items",                                    value:"Clothing Items"},
    {desc: "Towable Items",                                     value:"Towable Items"},
    {desc: "Supply Drops/Crashes",                              value:"Supply Drops/Crashes"},
    {desc: "Player/UI Function",                                value:"Player/UI Function"},
    {desc: "Website Feature Update",                            value:"Website Feature Update"}
];

var cFormItem = false;

$(function() {
    console.log("Ready");

    $(document).on('click', '#cr-submit', function(e) {
        e.preventDefault();
        submitForm();
    });

    populateForm();
});

function onloadCallback() {
    console.log("Captcha Ready.");
    cFormItem = grecaptcha.render('captcha', {
        'sitekey' : '6Lcs0lAdAAAAAPJt2kVxzzc9tFEM-dGfqjOvRBPO',
        'theme' : 'dark'
    });
};

function populateForm() {
    var html = '<option value="select-one" selected>Select One</option>';
    for (var r=0; r<requestTypes.length; r++) {
        html += "<option value='"+requestTypes[r].value+"'>"+requestTypes[r].desc+"</option>";
    }
    $('#request-type').html(html);

    var html = '<option value="select-one" selected>Select One</option>';
    for (var r=0; r<requestItems.length; r++) {
        html += "<option value='"+requestItems[r].value+"'>"+requestItems[r].desc+"</option>";
    }
    $('#request-item').html(html);
}

function submitForm() {
    if ($('#request-type').val() == "select-one") {
        return alert("Select a type of request.");
    }

    if ($('#request-item').val() == "select-one") {
        return alert("Select a specific item related to request. ");
    }

    if ($('#request-short-detail').val() == "") {
        return alert("Enter a short description about your change request.");
    }

    if ($('#request-detail').val() == "") {
        return alert("Enter some detail about your change request so we can look into.");
    }

    if ($('#request-author').val() == "") {
        return alert("Enter your name or Discord ID so we know who is requesting the change.");
    }

    if (grecaptcha.getResponse(cFormItem) == "") {
        return alert('You must check the "I\'m not a robot" checkbox, or are you a robot?');
    }

    $('#cr-submit').attr('disabled', 'disabled');
    $('#request-item').attr('disabled', 'disabled');
    $('#request-type').attr('disabled', 'disabled');
    $('#request-short-detail').attr('disabled', 'disabled');
    $('#request-detail').attr('disabled', 'disabled');
    $('#request-author').attr('disabled', 'disabled');

    $.post(
        'change-requests-async.php',
        {
            'cmd':                   'submit',
            'request-item':           $('#request-item').val(),
            'request-type':           $('#request-type').val(),
            'request-short-detail':   $('#request-short-detail').val(),
            'request-detail':         $('#request-detail').val(),
            'request-author':         $('#request-author').val(),
            'request-captcha':        grecaptcha.getResponse(cFormItem)
        },
        function (data) {
            if (data.status == "Ok") {
                window.location.href = '/change-request';
                alert("Your change request has been entered.");
            }
            else {
                alert(data.response);
                $('#cr-submit').removeAttr('disabled');
                $('#request-item').removeAttr('disabled');
                $('#request-type').removeAttr('disabled');
                $('#request-short-detail').removeAttr('disabled');
                $('#request-detail').removeAttr('disabled');
                $('#request-author').removeAttr('disabled');
            }
        },
        'json'
    );
}
