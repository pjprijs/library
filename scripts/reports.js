/*
showBooks(); getBookDetails(bookId);

*/
function initReports() {
    $('.report-select-group').select2({
        language: langCode,
        placeholder: txtAllGroups,
        multiple: true,
        data: parseActiveGroups(groupArray),
        width: '100%'
    }).on('change.select2', function (e) {
        reportGroupChanged();
    });
    reportGroupChanged();
}

function reportGroupChanged() {
    let tmpGroups = new Array();
    for(i in $('.report-select-group').select2('data')) {
        tmpGroups[tmpGroups.length] = $('.report-select-group').select2('data')[i].id;
    }
    setCookie('selectedGroups', tmpGroups, 180);
    getUserForToday(tmpGroups, true, false, 'notloaned');
    getUserForToday(tmpGroups, false, true, 'notsubmitted');
    getUserForToday(tmpGroups, false, false, 'nobook');
    getUserForToday(tmpGroups, true, true, 'done');
    getUserBookLoaned(tmpGroups);
    getTopXLoaned(tmpGroups);
}

function parseActiveGroups(data) {
    let newData = new Array();
    let selectedGroups = getCookie('selectedGroups').split(',');
    for(i in data) {
        if(data[i].active == '1') {
            let index = newData.length;
            newData[index] = {};
            newData[index].id = data[i].id;
            newData[index].text = txtGroup + ' ' + data[i].text;
            newData[index].selected = selectedGroups.includes(data[i].id);
        }
    }
    return newData;
}

function getUserForToday(selectedGroups, submitted, loaned, elem) {
    doLoad('getUserForToday.php', {
        selectedGroups: selectedGroups,
        submitted: submitted,
        loaned: loaned,
        elem: elem
    }, function(success, data){
        if(success) {
            showUserForToday(data);
        }
    });
}

function showUserForToday(data) {    
    $('.reports-user-book-' + data.elem).html('');
    for(i in data.results) {
        let tmpText = data.results[i].fullname + ' - ' + txtGroup + ' ' + data.results[i].groupname; 
        $('.reports-user-book-' + data.elem).append(
            $('<tr>').append($('<td>').append(tmpText))
        );
    }
}

function getUserBookLoaned(selectedGroups) {
    doLoad('getUserBookLoaned.php', {
        selectedGroups: selectedGroups
    }, function(success, data){
        if(success) {
            showUserBookLoaned(data);
        }
    });
}

function showUserBookLoaned(data) {    
    $('.reports-user-book-loaned').html('');
    for(i in data) {
        $('.reports-user-book-loaned').append(
            $('<tr>').append(
                $('<td>').append(data[i].user.fullname)
            ).append(
                $('<td class="clickable" onClick="showBooks(); getBookDetails(' + data[i].book.id + ');">').append(data[i].book.fulltitle)
            ).append(
                $('<td>').append(data[i].days)
            )
        );
    }
}
//getTopXLoaned

function getTopXLoaned(selectedGroups) {
    doLoad('getTopXLoaned.php', {
        selectedGroups: selectedGroups,
        limit: 20
    }, function(success, data){
        if(success) {
            showTopXLoaned(data);
        }
    });
}

function showTopXLoaned(data) {    
    $('.reports-top-x-loaned').html('');
    for(i in data) {
        $('.reports-top-x-loaned').append(
            $('<tr>').append(
                $('<td class="clickable" onClick="showBooks(); getBookDetails(' + data[i].book.id + ');">').append(data[i].book.fulltitle)
            ).append(
                $('<td>').append(data[i].amount)
            )
        );
    }
}
