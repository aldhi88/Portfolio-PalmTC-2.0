// $(function() {
    // for loader script
    $('.loading').fadeOut(500);

    // function for show alert element
    function previewImg(input, preview) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $(preview).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }

    function dbAlert(id){
        el = '<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                '<i class="mdi mdi-block-helper mr-2"></i>'+
                'Database error!'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                    '<span aria-hidden="true">×</span>'+
                '</button>'+
            '</div>'
        ;
        $("span#"+id).empty().append(el);
    }
    function showAlert(type, icon, id, msg){
        el = '<div class="alert alert-dismissible alert-'+type+'">'+
                    '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        if(Array.isArray(msg)){
            msg.forEach(myFunction);
            function myFunction(value, index, array) {
                el += '<p class="m-0 p-0"><i class="fa fa-'+icon+' fa-fw"></i> '+value+'</p>';
            }
        }else{
            el += '<p class="m-0 p-0"><i class="fa fa-'+icon+' fa-fw"></i> '+msg+'</p>';
        }
        el += '</div>';
        $("span#"+id).empty().append(el);
    }

    // function for clear error msg in the form
    function resetError(){
        $("input").keypress(function(){
            $(this).closest(".form-group").removeClass("has-error").find(".help-block").empty();
        })
    }

    // function form show or hide loader
    function loader(value){
        if(value==false){
            $('.loading').fadeOut(100);
        }else{
            $('.loading').fadeIn(100);
        }
    }


// });
