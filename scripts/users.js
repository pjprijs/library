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
                $('<td>').append(
                    $('<span class="users-list-' + data[i].id + '-text">' + data[i].fullname + '</span>')
                ).append(
                    $('<div class="users-list-' + data[i].id + '-edit hidden">').append(
                        $('<div class="input-group">').append(
                            $('<div class="input-group-prepend">').append(
                                $('<div class="input-group-text">' + txtFirstname + ':</div>')
                            )
                        ).append(
                            $('<input width="10" type="text" class="form-control users-list-' + data[i].id + '-name" placeholder="' + txtFirstname + '" value="' + data[i].name + '"/>')
                        )
                    ).append(
                        $('<div class="input-group">').append(
                            $('<div class="input-group-prepend">').append(
                                $('<div class="input-group-text">' + txtPrefix + ':</div>')
                            )
                        ).append(
                            $('<input type="text" class="form-control users-list-' + data[i].id + '-prefix" placeholder="' + txtPrefix + '" value="' + data[i].prefix + '"/>')
                        )
                    ).append(
                        $('<div class="input-group">').append(
                            $('<div class="input-group-prepend">').append(
                                $('<div class="input-group-text">' + txtLastname + ':</div>')
                            )
                        ).append(
                            $('<input type="text" class="form-control users-list-' + data[i].id + '-surname" placeholder="' + txtLastname + '" value="' + data[i].surname + '"/>')
                        )
                    )
                )
            ).append(
                $('<td>' + txtGroup + ' ' + (data[i].groupname != '' ? data[i].groupname : data[i].schoolyear) + '</td>')
            ).append(
                $('<td>').append(
                    $('<div class="users-list-' + data[i].id + '-icon">').append(
                        $('<i class="fas fa-pen clickable" onclick="userEdit(' + data[i].id + ')" data-toggle="tooltip" title="' + txtEdit + '"></i>')
                    )
                )
            ));
    }
}

function userEdit(id) {
    // users-list-' + data[i].id + '-text 
  if($('.users-list-' + id + '-text').is(":visible")) {
    $('.users-list-' + id + '-text').hide();
    $('.users-list-' + id + '-edit').show();
    $('.users-list-' + id + '-icon').html(
        $('<button type="button" class="btn btn-success mx-3">' + txtSave + '</button>')
    ).append(
        $('<button type="button" class="btn btn-danger" onclick="userEdit(' + id + ')">' + txtCancel + '</button>')
    );
  } else {
    $('.users-list-' + id + '-edit').hide();
    $('.users-list-' + id + '-text').show();
    $('.users-list-' + id + '-icon').html(
        $('<i class="fas fa-pen clickable" onclick="userEdit(' + id + ')" data-toggle="tooltip" title="' + txtEdit + '"></i>')
    );

  }
}