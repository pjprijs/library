let defaultModal = null;
let defaultModalActive = false;
let defaultModalFunction = function(){};
let defaultModalExitFunction = function(){};
let defaultModalData = {};

$(function() {
    defaultModal = new bootstrap.Modal(document.getElementById('defaultModal'), {
        backdrop: 'static',
        keyboard: true
    });     
    $("#defaultModal").on("hidden.bs.modal", function () {
        // put your default event here
        defaultModalExitFunction();
        defaultModalExitFunction = function(){};
        defaultModalActive = false;
    }); 
});

function setModal(titleText, bodyText, buttonText, buttonVisible){
    if(buttonVisible === undefined) buttonVisible = true;
    $('.modal-title').html(titleText);
    $('.modal-body').html(bodyText);
    $('.modal-dialog-button-ok').html(buttonText);
    $('.modal-dialog-button-ok').hide();
    if(buttonVisible) $('.modal-dialog-button-ok').show();
    defaultModal.show();
    defaultModalActive = true;
}

function modalSubmit() {
    defaultModalActive = false;
    defaultModal.hide();
    defaultModalFunction(defaultModalData);
    defaultModalFunction = function(){};
}