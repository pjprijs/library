function initReports() {
    /*
    console.log(groupArray);
    $('.report-select-group').select2({
        language: langCode,
        tags: true,
        multiple: true,
        tokenSeparators: [',', ';'],
        data: parseActiveGroups(groupArray),
        width: '100%'
    });
    */
}

function parseActiveGroups(data) {
    let newData = new Array();
    newData[0] = {
        id: '0',
        text: txtAllGroups
    };
    for(i in data) {
        if(data[i].active == '1') {
            newData[newData.length] = data[i];
        }
    }
    return newData;
}