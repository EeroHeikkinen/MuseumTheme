// Original idea by Janko Jovanovic
// http://www.jankoatwarpspeed.com/post/2009/07/28/reinventing-drop-down-with-css-jquery.aspx
//
// Modifications by NDL

// $(document).ready(function() {
    createDropDown();
    
    $(".dropdown dt a").click(function() {
        $(".dropdown dd ul").toggle();
        return false;
    });

    $(document).bind('click', function(e) {
        var $clicked = $(e.target);
        $(".dropdown dt a img").show();

        if (! $clicked.parents().hasClass("dropdown"))
            $(".dropdown dd ul").hide();
    });
                
    $(".dropdown dd ul li a").click(function() {
        var text = $(this).html();
        $(".dropdown dt a").html(text);
        $(".dropdown dd ul").hide();
        
        var source = $(".searchForm_styled");
        source.val($(this).find("span.value").html());
        return false;
    });
// });

function createDropDown(){
    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1] + '/';

    var source = $(".searchForm_styled");
    var selected = source.find("option[selected]");
    var options = $("option", source);
    var classname = $(".searchForm_styled").attr("class");

    $(".searchForm_styled").hide();    
    $(".searchForm_styled").before('<dl id="target" class="dropdown small' + classname + '"></dl>')
    $("#target").append('<dt><a href="#" class="hefty"><p>' + selected.text() + 
'</p><span class="value">' + selected.val() + 
'</span><img src="' + base_url + 'interface/themes/institution/images/dropdown_arrow.png" alt="v&nbsp;" /></a></dt>')
    $("#target").append('<dd><ul></ul></dd>')

    options.each(function(){
        $("#target dd ul").append('<li><a href="#" class="big"><p>' + 
            $(this).text() + '</p><span class="value">' + 
            $(this).val() + '</span><img src="' + base_url + 'interface/themes/institution/images/dropdown_arrow.png" alt="v&nbsp;" /></a></li>');
    });
}
