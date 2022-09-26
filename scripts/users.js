let currentUserStartPage = 0;
let maxUsersPerPage = 10;
let userSearchValue = null;

function userOverviewNext() {
    currentUserStartPage += maxUsersPerPage;
    getUsers();
}

function userOverviewPrevious() {
    currentUserStartPage -= maxUsersPerPage;
    if(currentUserStartPage < 0) currentUserStartPage = 0;
    getUsers();
}

function searchUser() {
    currentUserStartPage = 0;
    userSearchValue = $('.user-search-input').val();
    getUsers();
}

function getUsers() {
    let searchValue = userSearchValue;
    if(searchValue != null) {
        if(searchValue.length < 3) searchValue = null;
    }
    doLoad('getUsers.php', {
        search: searchValue,
        page: (currentUserStartPage/maxUsersPerPage)+1,
        limit: maxUsersPerPage,
        orderField: 's.name, u.name, u.surname',
        orderDir: 0
    }, function(success, data){
        if(success) {
            makeUserOverview(data);
        }
    });
}

function makeUserOverview(allData) {
    let data = allData.results;
    let pages = Math.ceil(allData.amount/maxUsersPerPage);
    $('.users-overview-header').html(txtPage + ' ' + ((currentUserStartPage/maxUsersPerPage)+1) + ' ' + txtOf + ' ' + pages + ' (' + allData.amount + ' ' + txtUsers + ')');
    $('.users-list').html('');
    $('.user-overview-previous').addClass('disabled');
    $('.user-overview-next').addClass('disabled');
    if(currentUserStartPage > 0) {
        $('.user-overview-previous').removeClass('disabled');
    }
    if(data.length >= maxUsersPerPage) {
        $('.user-overview-next').removeClass('disabled');
    }
    for(i=0; i<data.length; i++) {
        $('.users-list').append(
            $('<tr>').append(
                $('<td>' + data[i].fullname + '</td>')
            ).append(
                $('<td>' + txtGroup + ' ' + (data[i].groupname != '' ? data[i].groupname : data[i].schoolyear) + '</td>')
            ));
    }
}