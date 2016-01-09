$(document).ready(function() {
    $("#submit").click(function() {
        var eid = $.trim($("#username").val());
        var pass = $.trim($("#password").val());
        var name = $.trim($("#name").val());
        var email = $.trim($("#email").val());
        var gradyear = $.trim($("#gradyear").val());
        
        name = name.replace(/\ /g,'');
        name = name.replace(/\'/g,'');
        name = name.replace(/\./g,'');
        name = name.replace(/\ /g,'');
        name = name.replace(/\//g,'');
        

        var access = $("#access").val();
        var dataString = 'eid='+eid+'&pass='+pass+'&name='+name+'&email='+email+'&gradyear='+gradyear+'&access='+access;
        if (eid === "" || pass === "" || name ==="" || email === "" || gradyear === "" || access === "") {
            alert("Please Fill All Fields/Your Password must contain numbers and letters; please change it first!");
        } else {
            $.ajax({
                type: "POST",
                url: "addQueue.php",
                data: dataString,
                cache: false,
                success: function(result) {
                    if (result.indexOf("Success") == -1){
                        alert(result);
                        $(".form-horizontal")[0].reset();
                    }
                    else {
                      alert(result);
                    }
                }
            });
        }
        return false;
    });
});