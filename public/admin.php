<?php

require '../vendor/autoload.php';

//Ajax Librairy ----
// Get the core singleton object
// and the Response class

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';
require_once("../includes/fct_display.php");
require_once("../includes/fct_admin_event.php");
$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';

$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

require_once("../includes/fct_security.php");

if(isset($user) && $user->isLoggedIn() && $user->data()->permissions == 2){
     if (ip_blocked()){
        $user->logout();
        Redirect::to('blocked.php');        
    }

    else {
        $account = $user->data();
        $fname=$account->fname;$lname=$account->lname;
        $email=$account->email;
        $uid=$account->fd_id;
        $Logged = $fname;
    }
}
else {
    $Logged=false;
    $_SESSION['goto'] = 'admin.php';
    //$uri= urlencode('admin.php');
    Redirect::to('login.php');
  
}
?>
<!-- ajax Script -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
<!-- jQuery UI -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>


<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script>
<script>
    function get_editordata() {
        $('#editordata_en').val($('#summernote_en').summernote('code'));
        $('#editordata_jp').val($('#summernote_jp').summernote('code'));
        return true;
    }
</script>
    <div id="wrapper">
        <?php echo display_navbar($Logged); ?>
        
        <section>
            <div class="container">
              
                <?php //echo displayEventsList(); ?>
                <div id="event_list"></div>
                <hr>
                <div id="event_detail"></div>
                
                <div id="attendees_detail"></div>
                <div id="displaymodal">
                        <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form class="eventInsForm" id="eventAddAttd" onsubmit="return jaxon_addAttendees_prepend(jaxon.getFormValues('eventAddAttd'))">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">				
                                        <h4 class="modal-title" id="myModalLabel"></h4>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        <div id="small-modal-content">
                                            <label for='user_input'>Select user:</label>
                                            <input type="text" id="user_input" class="userpicker"/>
                                            <div class='invisible' >
                                                <input type="text" id="user_id" name="user_id"/>
                                                <input type="text" id="schedule_id" name="schedule_id" />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer" id="modal-footer"></div>
                                </div>
                                </form>
                            </div>
                        </div>
                </div>
                
            </div>          
        </section>
			
<!-- Modal start -->
        <?php echo displayModalAddSchedule(); ?>
<!-- Modal end -->
    </div>
<!-- /wrapper -->


<!-- SCROLL TO TOP -->
<a href="#" id="toTop"></a>

 <?php
  $jaxon = jaxon();
    echo $jaxon->getJs();
    echo $jaxon->getScript();
?>
<script>
        $( function() {

            // Single Select
            $( "#user_input" ).autocomplete({
                source: function( request, response ) {
                    // Fetch data
                    $.ajax({
                     url: 'ajax.php',
                     type: 'post',
                     dataType: "json",
                     data: {
                      search: request.term
                     },
                     success: function( data ) {
                      response( data );
                     }
                    });
                },
                select: function (event, ui) {
                 // Set selection
                 var user_data = ui.item.label.split(" - ");
                 $('#user_input').val(user_data[0]); // display the selected text
                 $('#user_id').val(ui.item.value); // save selected id to input
                 
                 console.log(ui.item.label, ui.item.value);
                 return false;
                }
            });
            
           });
           
        </script>
<!-- PRELOADER -->
<div id="preloader">
        <div class="inner">
                <span class="loader"></span>
        </div>
</div><!-- /PRELOADER -->

		<!-- JAVASCRIPT FILES -->
		<script>var plugin_path = 'assets/plugins/';</script>
		<!--<script src="assets/plugins/jquery/jquery-3.3.1.min.js"></script> -->

		<script src="assets/js/scripts.js"></script>
		

		<!-- PAGE LEVEL SCRIPTS -->
		<script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
		<script src="assets/plugins/datatables/js/dataTables.tableTools.min.js"></script>
		<script src="assets/plugins/datatables/js/dataTables.colReorder.min.js"></script>
		<script src="assets/plugins/datatables/js/dataTables.scroller.min.js"></script>
		<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
		<script src="assets/plugins/select2/js/select2.full.min.js"></script>
                <script src="assets/plugins/select2/js/select2.full.min.js"></script>
                <script type="text/javascript" src="assets/plugins/bootstrap.datepicker/js/bootstrap-datepicker.min.js"></script>

		<script>

			if (jQuery().dataTable) {

				function initTable6() {
					var table = jQuery('#datatable_sample');

					table.dataTable({
						"columns": [{
							"orderable": true
						}, {
							"orderable": true
						}, {
							"orderable": true
						}, {
							"orderable": false
						}, {
							"orderable": false
						}],
						"lengthMenu": [
							[5, 15, 20, -1],
							[5, 15, 20, "All"] // change per page values here
						],
						// set the initial value
						"pageLength": 5,            
						"pagingType": "bootstrap_full_number",
						"language": {
							"lengthMenu": "  _MENU_ records",
							"paginate": {
								"previous":"Prev",
								"next": "Next",
								"last": "Last",
								"first": "First"
							}
						},
						"columnDefs": [{  // set default column settings
							'orderable': false,
							'targets': [0]
						}, {
							"searchable": false,
							"targets": [0]
						}]
					});

					var tableWrapper = jQuery('#datatable_sample_wrapper');

					table.find('.group-checkable').change(function () {
						var set = jQuery(this).attr("data-set");
						var checked = jQuery(this).is(":checked");
						jQuery(set).each(function () {
							if (checked) {
								jQuery(this).attr("checked", true);
								jQuery(this).parents('tr').addClass("active");
							} else {
								jQuery(this).attr("checked", false);
								jQuery(this).parents('tr').removeClass("active");
							}
						});
						jQuery.uniform.update(set);
					});

					table.on('change', 'tbody tr .checkboxes', function () {
						jQuery(this).parents('tr').toggleClass("active");
					});

					tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown

				}
                            initTable6();
   


			}

		</script>

<script>var plugin_path = 'assets/plugins/';</script>
<script type="text/javascript" src="assets/plugins/form.validate/jquery.validation.min.js"></script>

<script type="text/javascript">window.jaxon_displayEventsList();</script>

</body>
</html>