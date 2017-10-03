$(document).ready(function() {
    var browser = get_browser();
    console.log(browser);
    if(browser == 'Chrome') {
        $('.plugin-chrome').removeClass('hidden');
    } else if(browser == 'Firefox') {
        $('.plugin-firefox').removeClass('hidden');
    }
});

function get_browser() {
    if((navigator.userAgent.indexOf("Opera") || navigator.userAgent.indexOf('OPR')) != -1 )
    {
        return 'Opera';
    }
    else if(navigator.userAgent.indexOf("Chrome") != -1 )
    {
        return 'Chrome';
    }
    else if(navigator.userAgent.indexOf("Safari") != -1)
    {
        return 'Safari';
    }
    else if(navigator.userAgent.indexOf("Firefox") != -1 )
    {
        return 'Firefox';
    }
    else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) //IF IE > 10
    {
        return 'IE';
    }
    else
    {
        return 'unknown';
    }
}