$(document).ready(function(){

    //input datepicker
    $(".datepicker").datepicker({dateFormat: 'dd/mm/yy'});

    //icon datepicker
    $('.fa-calendar').click(function(){
        $(".datepicker").focus();
    });

    //validate input
    $("#create").validate({
        rules: {
            Book_Name: "required",
            Book_Type: "required",
            Book_Date: {
                required: true,
                date: 'dd/mm/yyyy'
            }
        },
        messages:{
            Book_Name: "Please input name book",
            Book_Type: "Please input type book",
            Book_Date: {required: "Please input date have format dd/mm/yyyy", date:"Date format dd/mm/yyyy"}
        }
    });

    $("#update").validate({
        rules: {
            Book_Name: {
                required: true,
                minlength: 6
            },
            Book_Type:  {
                required: true,
                minlength: 6
            },
            Book_Date: {
                required: true,
            }
        },
        messages:{
            Book_Name: "Please input name book",
            Book_Type: "Please input type book",
            Book_Date: {required: "Please input date have format dd/mm/yyyy"}
        }
    });

});


