var arrayApps = new Array();

$(document).ready(function () {
    $.ajax({
        url: '/res/applications.json',
        method: 'POST',
        data: "load",
        dataType: 'JSON',
        success: function (data) {
            arrayApps = data;
            draw(arrayApps);
        }
    });
});

function draw(ar) {
    var tmp = '';
    var nPinned = 0;

    for (i = 0; i < ar.length; i++) {
        //if(ar[i].pinned == 1 && ar[i].legacy == 0){
        tmp += '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">';
        tmp += '<div class="card-pf card-pf-view card-pf-view-select">';
        tmp += '<div class="card-pf-body text-center">';
        tmp += '<div class="card-pf-top-element"></div>';
        tmp += '<img src="' + ar[i].icon_base64 + '" width="90"></img>';
        tmp += '</div><h2 class="card-pf-title text-center">' + ar[i].name + '</h2>';
        tmp += '<p class="card-pf-info text-center">' + ar[i].description + '<br><em class="version-p">v' + ar[i].release.version + '</em></p>';
        tmp += '<div class="card-pf-items text-center"><div class="card-pf-item"><a target="_blank" href="' + ar[i].url + '">Launch</a></div></div>';
        tmp += '</div></div></div>';

        nPinned++;
        //}
    }

    if (nPinned <= 0) {
        $("#apps").html('<div class="blank-slate-pf"><h3>No applications found</h3><p>You can add application by pinning it on the application list on Cockpit</p></div>');
    } else {
        $("#apps").html(tmp);
    }
}

function openCockpit() {
    window.open("http://" + window.location.hostname + ":9090");
}