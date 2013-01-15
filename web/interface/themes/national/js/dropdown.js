// Original idea by Janko Jovanovic
// http://www.jankoatwarpspeed.com/post/2009/07/28/reinventing-drop-down-with-css-jquery.aspx
//
// Modifications by NDL



$(document).ready(function() {
    
    createDropDowns();

    $(".dropdown dt a").bind('click', function() {
        var dropdown = $(this).closest('dl.dropdown');
        dropdown.find('dd ul').fadeIn(100);
        return false;
    });
    
    // Hide dropdown when clicking outside
    $(document).bind('click', function(e) {
        var clicked = $(e.target);
        if (! clicked.parents().hasClass("dropdown"))
            $(".dropdown dd ul").hide();
    });

    $(".dropdown dd ul li a").bind('click', function() {
        var dropdown = $(this).closest('dl.dropdown');
        var text = $(this).html();
        dropdown.find('dt a').html(text);
        dropdown.find('dd ul').fadeOut(100);
        
        // Get id of the hidden select element
        var source = dropdown.next('select');
        
        source.find('option').removeAttr('selected');
        source.find('option[value="'+$(this).find("span.value").text()+'"]').attr('selected', 'selected').change();
        return false;
    });    

// Function for creating dropdowns
function createDropDowns(){
    $('.styledDropdowns, .searchForm_styled, .jumpMenu').each(function() { 
        var source = $(this);
        var selected = source.find("option[selected]");
        var options = $("option", source);
        var idName = $(this).attr("id");
        var target = 'styled_'+idName;

        $(this).hide().before('<dl id="'+target+'" class="dropdown ' + idName + '"></dl>');
        $('#'+target).append('<dt><a href="#">' + selected.text() + 
    '<span class="value">' + selected.val() + 
    '</span></a></dt>');
        $("#"+target).append('<dd><ul></ul></dd>');

        options.each(function(){
            if ($(this).text()) {
                $("#"+target+" dd ul").append('<li><a href="#" class="big">' + 
                    $(this).text() + '<span class="value">' + 
                    $(this).val() + '</span></a></li>');
            } else {
                $("#"+target+" dd ul").append('<li style="height: 2px"><hr/></li>');
            }
        });
    })
