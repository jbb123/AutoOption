
$(function(){
    var $resultsoutput = $('#results');
    $('#year').change(function getmakes(){
        var vehyear = $("#year").val();
        $("#model").html('<option value="">-- All Models --</option>');
        $.ajax({
               url: "/includes/module/search/ajax/makes.php",
               global: false,
               type: "POST",
               async: false,
               dataType: "html",
               data: "year="+vehyear, //the name of the $_POST variable and its value
               success: function (response) //'response' is the output provided
                           {
                        //counts the number of dynamically generated options
                        var dynamic_options = $("*").index( $('.dynamic')[0] );
                        //removes previously dynamically generated options if they exists (not equal to 0)
                        if (dynamic_options != (-1)) $(".dynamic").remove();
                        $("#make").html(response);
                        $(".first").attr({selected: ' selected'});
                        
                       }
              });
              return false
    });
    
    $('#make').change(function getmodels(){
        var vehyear = $("#year").val();
        var vehmake = $("#make").val();
        $.ajax({
               url: "/includes/module/search/ajax/models.php",
               global: false,
               type: "POST",
               async: false,
               dataType: "html",
               data: "year="+ vehyear+"&make="+vehmake, //the name of the $_POST variable and its value
               success: function (response) //'response' is the output provided
                           {
                         //counts the number of dynamically generated options
                        var dynamic_options = $("*").index( $('.dynamic')[0] );
                        //removes previously dynamically generated options if they exists (not equal to 0)
                        if (dynamic_options != (-1)) $(".dynamic").remove();
                        $("#model").html(response);
                        $(".first").attr({selected: ' selected'});                        
                       }
              });
              return false
    });
    $('#model').change(function getstyles(){
        var vehyear = $("#year").val();
        var vehmake = $("#make").val();
        var vehmodel = $("#model").val();
        $.ajax({
               url: "/includes/module/search/ajax/trims.php",
               global: false,
               type: "POST",
               async: false,
               dataType: "html",
               data: "year="+ vehyear+"&make="+vehmake+"&model="+ vehmodel, //the name of the $_POST variable and its value
               success: function (response) //'response' is the output provided
                           {
                        //counts the number of dynamically generated options
                        var dynamic_options = $("*").index( $('.dynamic')[0] );
                        //removes previously dynamically generated options if they exists (not equal to 0)
                        if (dynamic_options != (-1)) $(".dynamic").remove();
                        $("#trim").html(response);
                        $(".first").attr({selected: ' selected'});
                        
                       }
              });
              return false
    });               

    
    $('form#cardbform select').change(function(){
        if (!$(this).val()){ 
            $resultsoutput.html(''); 
            return; 
        }
        var content = "<b>Selected:</b> " 
            + $('#year').val() + ' '
            + $('#make').val() + ' '
            + $('#model').val() + ' '
            + $('#trim').val() + ' '
            + $('#bodystyle').val();
        $resultsoutput.html(content);    
    });
});