/*
 MOMENTS

*/
jQuery(document).ready(function($){
            $('#create_new_site_form_now').on('submit', function(event){
                $('#showdatregistrationState').html('Doing registration........<img src="'+mtfx_spinner_gif+'">');
                event.preventDefault();
                var formdaa = $(this).serialize()+'&token=UTKK8JN48H98SO0Q3JG7DCBHJURHFY34HD&action=mtfxajaxRegistrationPHP';
                $.ajax({
                     url:mtfx_ajaxurl,
                     type:'post',
                     data:formdaa,
                     dataType:'json',
                     success:function(rdata){
                        if(rdata.status=='OK'){
                            $('#showdatregistrationState').html('<div class="alert alert-success">'+rdata.message+'</div>');
                        }
                        else if(rdata.status=='NK'){
                             $('#showdatregistrationState').html('<div class="alert alert-danger">'+rdata.message+'</div>');
                        }
                      
                     },
                     error:function(rdata){
                         $('#showdatregistrationState').html('<div class="alert alert-danger">'+rdata.responseText+'</div>');
                     }

                });

            });

            $('#create_new_site_form_by_existinguser').on('submit', function(event){
                $('#showdatregistrationState').html('Doing site creation........<img src="'+mtfx_spinner_gif+'">');
                event.preventDefault();
                var formdaa = $(this).serialize()+'&token=W98L7LP3WSI8JHFYRH98SO0Q3JGOLAQ4JURHFY34HD&action=mtfxajaxRegistrationLoggedInPHP';
                $.ajax({
                     url:mtfx_ajaxurl,
                     type:'post',
                     data:formdaa,
                     dataType:'json',
                     success:function(rdata){
                        if(rdata.status=='OK'){
                            $('#showdatregistrationState').html('<div class="alert alert-success">'+rdata.message+'</div>');
                        }
                        else if(rdata.status=='NK'){
                             $('#showdatregistrationState').html('<div class="alert alert-danger">'+rdata.message+'</div>');
                        }
                      
                     },
                     error:function(rdata){
                         $('#showdatregistrationState').html('<div class="alert alert-danger">'+rdata.responseText+'</div>');
                     }

                });

            });

});
