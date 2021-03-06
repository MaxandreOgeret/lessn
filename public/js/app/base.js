$( document ).on('click', '.ajax-link', function (e) {
    e.preventDefault();

    var linkObject = $(this);
    var ajaxTarget = linkObject.attr('ajax-target');
    var ajaxTargetObject = $(ajaxTarget);
    var ajaxSource = linkObject.attr('href');
    var callback = linkObject.attr('ajax-callback');
    var errorCallback = linkObject.attr('ajax-error-callback');
    var func = linkObject.attr('func');

    // vars for tests
    var ajaxTargetIsDefined = typeof ajaxTarget !== "undefined" && ajaxTarget.trim() !== "";
    var funcIsDefined = typeof func !== "undefined" && func.trim() !== "";

    // Test source is defined
    if (typeof ajaxSource === "undefined" || ajaxSource.trim() === "") {
        throw "Ajaxlink : href is not defined";
    }

    // Test Either ajax-target or func is defined
    if (!(ajaxTargetIsDefined ^ funcIsDefined)) {
        throw "Either ajax-target or func must be defined.";
    }

    // test func function exists
    if ((funcIsDefined) && !functionExists(func) ) {
        throw "Ajaxlink : The function "+func+" does not exits.";
    }

    // Test ajaxtarget exists in the page
    if  (ajaxTargetIsDefined && ajaxTargetObject.length == 0) {
        throw "Ajaxlink : There is no "+ajaxTarget+" object in the page.";
    }

    $.ajax({
        url : ajaxSource,
        type : 'GET',
        dataType : 'json',
        success : function(data)
            {
                if(ajaxTargetIsDefined) {
                    ajaxTargetObject.html(data);
                } else if ((funcIsDefined) && functionExists(func)) {
                    window[func](data);
                }
            },
        complete : function(jqXHR) {if(functionExists(callback)) {window[callback]()} },
        error : function(jqXHR) {if(functionExists(errorCallback)) {window[errorCallback]()} }
    });

});

function functionExists(functionName) {
    return typeof window[functionName] !== 'undefined' && $.isFunction(window[functionName])
}

$( '#home_container' ).on('submit', ".ajax-form", function (e) {
    e.preventDefault();
    var form = $(this);
    var action = form.attr('action');
    var callback = form.attr('ajax-callback');

        $.ajax({
            type: 'POST',
            url: action,
            data: form.serialize(),
            dataType: 'json',
            success: function (data) {$('#home_container').html(data);},
            complete : function(jqXHR) {if(functionExists(callback)) {window[callback]()} }
        });

});

$('#home_container').on('click', '#copy-url', function (e) {
    $('#shortened-txtbox').select();
    document.execCommand("copy");
    $('#shortened-txtbox').tooltip({
        title: "Text copied !",
        trigger: 'manual',
        placement: "bottom"
    });
    $('#shortened-txtbox').tooltip('show');
    setTimeout(
        function()
        {
            $('#shortened-txtbox').tooltip('hide');
        }, 1000);
});