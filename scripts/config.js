
function toggleStyle(style) {
    setCookie('selectedStyle', style, 180);
    let navbarStyle = 'navbar-light bg-light';
    switch(style) {
        case 'spacelab':
        case 'minty':
            navbarStyle = 'navbar-dark bg-primary';
            break;
        case 'solar':
            navbarStyle = 'navbar-dark bg-dark';
            break;
    }
    /*
    if(style == 'spacelab' || style == 'minty' || style == 'solar') {
        navbarStyle = 'navbar-dark bg-dark';
    }
    */
    setCookie('navbarStyle', navbarStyle, 180);
    location.reload();
}

function toggleDebug() {
    if(debug.debug) {
        setDebug(false);
    } else {
        setDebug(true);
    }  
}

function setDebug(value) {
  if(value) {
    debug.debug = true;
    $('.menu-debug').html('<i class="fas fa-bug"></i> debug is on');
    $('.debug').show();
  } else {
    debug.debug = false;
    $('.menu-debug').html('<i class="fas fa-bug-slash"></i> debug is off');
    $('.debug').hide();
  }  
  setCookie('debug', debug.debug, 180);
}

function loadConfigItems(callback) {
    loadConfigItem('avi', function() {
        loadConfigItem('group', function(){
            callback();
        });
    });    
}

function loadConfigItem(item, callback) {
    doLoad('getItem.php', {
        table: item
    }, function(success, data){
        if(success) {
            eval(data.item + 'Array = data.data;');
            initConfigItem(data.item);
        }
        callback();
    });
}
function initConfigItem(item) {
    let itemArray = new Array();
    eval('itemArray = ' + item + 'Array;');
    $('.config-order-' + item).html('');
    for(i in itemArray) {
        $('.config-order-' + item).append(
            $('<li id="' + itemArray[i].id + '" value="' + itemArray[i].name + '" active="' + itemArray[i].active + '" class="list-group-item d-flex justify-content-between align-items-center clickable">').append(
                $('<div>').append(
                    $('<input class="form-check-input me-3 config-' + item + '-checkbox-' + itemArray[i].id + '" onchange="setConfigItemActive(\'' + item + '\', ' + itemArray[i].id + ')" type="checkbox" value=""' + (itemArray[i].active ==1 ? ' checked="checked"' : '') + '>')
                ).append(
                    $('<span class="me-3 config-item-' + item + '-name-text-' + itemArray[i].id + '">' + itemArray[i].name + '</span>')
                ).append(
                    $('<span class="config-item-' + item + '-name-input-' + itemArray[i].id + ' hidden"><input type="text" class="form-control me-3 w-auto d-inline-block" value="' + itemArray[i].name + '"/></span>')
                ).append(
                    $('<i class="fas fa-pen config-item-' + item + '-name-edit-' + itemArray[i].id + '" onclick="editConfigItem(\'' + item + '\', ' + itemArray[i].id + ')"></i>')
                ).append(
                    $('<i class="fas fa-floppy-disk me-3 hidden config-item-' + item + '-name-save-' + itemArray[i].id + '" onclick="saveConfigItem(\'' + item + '\', ' + itemArray[i].id + ')"></i>')
                ).append(
                    $('<i class="fas fa-xmark hidden config-item-' + item + '-name-save-' + itemArray[i].id + '" onclick="closeConfigItem(\'' + item + '\', ' + itemArray[i].id + ')"></i>')
                )
            ).append(
                $('<div class="float-right">').append(
                    $('<i class="fas fa-sort me-3">')
                ).append(
                    $('<i class="fas fa-trash-can clickable" onclick="delConfigItem(\'' + item + '\', ' + itemArray[i].id + ', \'' + itemArray[i].name + '\')">')
                )
            )
        );
    }
    $('.config-order-' + item).sortable({
        animation: 150,
        ghostClass: 'blue-background-class',
        onEnd: function (evt) {
            dataArray = new Array();
            $('.config-order-' + item + ' li').each(function(idx, elem) {
                dataArray[idx] = $(elem).attr('id');
            });
            doLoad('setItemOrder.php', {
                table: item,
                data: dataArray
            }, function(success, item) {
                loadConfigItem(item);
            });
        }
    });
}

function buildLevels(item) {
    let tmpArray = new Array();
    $('.config-order-' + item + ' li').each(function(idx, elem) {
        tmpArray[idx] = {};
        tmpArray[idx].id = $(elem).attr('id');
        tmpArray[idx].name = $(elem).attr('value');
        tmpArray[idx].name = $(elem).attr('active');
    });
    eval(item + 'Array = tmpArray;');
}

function addConfigItem(item) {
    let value = $('.config-' + item + '-input').val().trim()
    if(value != '') {
        doLoad('addItem.php', {
            table: item,
            value: value
        }, function(success, data){
            if(success) {
                eval(data.item + 'Array.push({id: data.id,name: data.name,active: data.active});');
            }
            initConfigItem(data.item);
        });
    } else {
        showErrorMsg(txtErrFieldsRequired + ': ' + txtReadLevel);
    }
}

function setConfigItemActive(item, id) {
    active = ($('.config-' + item + '-checkbox-' + id).is(':checked') ? 1 : 0);
    doLoad('setItemActive.php', {
        table: item,
        value: id,
        active: active
    }, function(success, data){
    });
}

function delConfigItem(item, id, name) {
    $('.config-' + item + '-input').val(name);
    doLoad('delItem.php', {
        table: item,
        value: id
    }, function(success, data){
        if(success) {
            loadConfigItem(data.item);
        }
    });
}

function editConfigItem(item, id) {
    $('.config-item-' + item + '-name-text-' + id).hide();
    $('.config-item-' + item + '-name-edit-' + id).hide();
    $('.config-item-' + item + '-name-save-' + id).show();
    $('.config-item-' + item + '-name-input-' + id).show();
    $('.config-item-' + item + '-name-input-' + id + ' input').focus();
}

function saveConfigItem(item, id) {
    newName = $('.config-item-' + item + '-name-input-' + id + ' input').val();
    doLoad('setItemName.php', {
        table: item,
        value: id,
        name: newName
    }, function(success, data){
        if(success) {
            $('.config-item-' + data.item + '-name-text-' + data.id).html(data.name);
        }        
        closeConfigItem(data.item, data.id);
    });
}

function closeConfigItem(item, id) {
    $('.config-item-' + item + '-name-text-' + id).show();
    $('.config-item-' + item + '-name-input-' + id).hide();
    $('.config-item-' + item + '-name-edit-' + id).show();
    $('.config-item-' + item + '-name-save-' + id).hide();
    $('.config-item-' + item + '-name-input-' + id + ' input').val($('.config-item-' + item + '-name-text-' + id).html());
}

function dbExport() {
    var win = window.open("", "Export database", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=400,height=100,top="+(screen.height-400)+",left="+(screen.width-840));
    win.document.body.innerHTML = "<h3>Downloading database export...</h3>";
    win.location.href='ajax/backupDb.php';
}