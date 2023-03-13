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
    let groupSelectOptions = yearSelectOptions = '';
    for(i=0; i<groupArray.length; i++) {
        if(groupArray[i].active == '1') {
            groupSelectOptions += '<option value=' + groupArray[i].id + '>' + groupArray[i].name + '</option>\n';
        }
    }
    for(i=1; i<10; i++) {
        yearSelectOptions += '<option value=' + i + '>Jaar: ' + i + '</option>\n';
    }
    $('.users-list').append(
        $('<tr>').append(
            $('<td colspan="3">').append(
                $('<div class="row">').append(
                    $('<div class="col-md-2">').append(
                        $('<input type="text" class="form-control users-list-new-name" placeholder="' + txtFirstname + '" value=""/>')
                    )
                ).append(
                    $('<div class="col-md-1">').append(
                        $('<input type="text" class="form-control users-list-new-prefix" placeholder="' + txtPrefix + '" value=""/>')
                    )
                ).append(
                    $('<div class="col-md-3">').append(
                        $('<input type="text" class="form-control users-list-new-surname" placeholder="' + txtLastname + '" value=""/>')
                    )
                ).append(
                    $('<div class="col-md-2">').append(
                        $('<select class="form-control users-list-new-group">').append(groupSelectOptions)
                    )
                ).append(
                    $('<div class="col-md-2">').append(
                        $('<select class="form-control users-list-new-year">').append(yearSelectOptions)
                    )
                ).append(
                    $('<div class="col-md-2">').append(
                        $('<button type="button" class="btn btn-primary" onclick="addUserInfo()">' + txtAdd + '</button>')
                    )
                )
            )
        )
    );
    for(i=0; i<data.length; i++) {
        $('.users-list').append(
            $('<tr>').append(
                $('<td>').append(
                    $('<span class="users-list-' + data[i].id + '-text">' + data[i].fullname + '</span>')
                ).append(
                    $('<div class="users-list-' + data[i].id + '-edit hidden">').append(
                        $('<input type="text" class="form-control users-list-' + data[i].id + '-name" placeholder="' + txtFirstname + '" value="' + data[i].name + '"/>')
                    ).append(
                        $('<input type="text" class="form-control users-list-' + data[i].id + '-prefix" placeholder="' + txtPrefix + '" value="' + data[i].prefix + '"/>')
                    ).append(
                        $('<input type="text" class="form-control users-list-' + data[i].id + '-surname" placeholder="' + txtLastname + '" value="' + data[i].surname + '"/>')
                    )
                )
            ).append(
                $('<td>').append(
                    $('<span class="users-list-' + data[i].id + '-text">' + txtGroup + ' ' + (data[i].groupname != '' ? data[i].groupname : data[i].schoolyear) + '</span>')
                ).append(
                    $('<div class="users-list-' + data[i].id + '-edit hidden">').append(
                        $('<select class="form-control users-list-' + data[i].id + '-group">').append(groupSelectOptions)
                    ).append(
                        $('<select class="form-control users-list-' + data[i].id + '-year">').append(yearSelectOptions)
                    )
                )
            ).append(
                $('<td style="text-align: right;">').append(
                    $('<div class="users-list-' + data[i].id + '-icon">').append(
                        $('<i class="fas fa-pen clickable mx-3" onclick="userEdit(' + data[i].id + ')" data-toggle="tooltip" title="' + txtEdit + '"></i>')
                    )
                )
            )
        );
        $('.users-list-' + data[i].id + '-group').val(data[i].groupId);
        $('.users-list-' + data[i].id + '-year').val(data[i].schoolyear);
    }
}

function userEdit(id) {
    // users-list-' + data[i].id + '-text 
  if($('.users-list-' + id + '-text').is(":visible")) {
    $('.users-list-' + id + '-text').hide();
    $('.users-list-' + id + '-edit').show();
    $('.users-list-' + id + '-icon').html(
        $('<button type="button" class="btn btn-success mx-3" onclick="setUserInfo(' + id + ')">' + txtSave + '</button>')
    ).append(
        $('<button type="button" class="btn btn-danger" onclick="userEdit(' + id + ')">' + txtCancel + '</button>')
    );
  } else {
    $('.users-list-' + id + '-edit').hide();
    $('.users-list-' + id + '-text').show();
    $('.users-list-' + id + '-icon').html(
        $('<i class="fas fa-pen clickable  mx-3" onclick="userEdit(' + id + ')" data-toggle="tooltip" title="' + txtEdit + '"></i>')
    );
  }
}

function setUserInfo(id) {
    doLoad('setUserInfo.php', {
        id: id,
        name: $('.users-list-' + id + '-name').val(),
        prefix: $('.users-list-' + id + '-prefix').val(),
        surname: $('.users-list-' + id + '-surname').val(),
        group: $('.users-list-' + id + '-group').val(),
        year: $('.users-list-' + id + '-year').val()
    }, function(success, data){
        if(success) {
            getUsers();
        }
    });
}

function addUserInfo(id) {
    doLoad('addUserInfo.php', {
        name: $('.users-list-new-name').val(),
        prefix: $('.users-list-new-prefix').val(),
        surname: $('.users-list-new-surname').val(),
        group: $('.users-list-new-group').val(),
        year: $('.users-list-new-year').val()
    }, function(success, data){
        if(success) {
            getUsers();
        }
    });
}