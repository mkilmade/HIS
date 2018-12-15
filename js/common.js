// Functions common to two or more pages (currently just add_winner.php & edit_winner.php)

// used by add_winner.php & edit_winner.php to auto fill jockey & 
// trainer fielss for selected horse

//invoked as the 'onblur' event handler of respective forms
function horse_trigger() {
    var horse=$("#horse").val();

    // make ajax call if horse is set but both trainer & jockey are not yet set in form
    if (horse!="" && $("#trainer").val()=="" && $("#jockey").val()=="") {

      // build query info for GET
      var queryData = new Object();
      queryData.type = 'last_win_data';
      queryData.horse = horse;

      // build settings/options for $.ajax call
      var options = new Object();
      options.data = queryData;
      options.dataType = "json";
      options.method = "GET";
      options.success = function(response, status, xhr) {
        $("#trainer").val(response.trainer);
        $("#jockey").val(response.jockey);
      }
      options.error = function(xhr, status, errorThrown) {
        console.log("An error has occcured in 'horse_trigger' function:");
        console.log("       Status: " + xhr.status + " - " + xhr.statusText);
        console.log("Response Text: " + xhr.responseText);
      }
      options.url = "getHisInfo.php";

      $.ajax(options);
    }
}

function field_size_onblur() {
	if ($('#odds').val() == 0.0) {
		$('#odds').focus();
	}
}

function race_date_trigger(e) {
	var race_date=$("#race_date").val();
	
    // build request for GET
    var request = new Object();
    request.type = 'next_race';
    request.race_date = race_date;

    // build settings/options for $.ajax call
    var options = new Object();
    options.data = request;
    options.dataType = "json";
    options.method = "GET";
    options.success = function(response, status, xhr) {
      $("#race").val(response.next_race);
      $("#distance").focus();
    }
    options.error = function(xhr, status, errorThrown) {
      console.log("An error has occcured in request for next race #:");
      console.log("       Status: " + xhr.status + " - " + xhr.statusText);
      console.log("Response Text: " + xhr.responseText);
    }
    options.url = "getHisInfo.php";

    $.ajax(options);	
}

function showIndividualStats(domain, name) {
  $.ajax({
     url: "getHisInfo.php",
     method: 'GET',
     dataType: 'json',
     data: {
      type: "individual_stats",
      name: name,
      domain: domain
     },
     success: function( response, status, xhr ) {
         $("#individual_info").html(response.html);
         $("#individual_info").css('visibility', 'visible');
     },
     error: function(xhr, status, errorThrown) {
         console.log("An error has occcured in request for individual stats:");
         console.log("       Status: " + xhr.status + " - " + xhr.statusText);
         console.log("Response Text: " + xhr.responseText);
     }
  });
}

function showRaceSummaryInfo(race_id) {
	  $.ajax({
	     url: "getHisInfo.php",
	     method: 'GET',
	     dataType: 'json',
	     data: {
	      type: "race_summary",
	      race_id: race_id
	     },
	     success: function( response, status, xhr ) {
	         $("#race_summary").html(response.html);
	         $("#race_summary").css('visibility', 'visible');
	     },
	     error: function(xhr, status, errorThrown) {
	         console.log("An error has occcured in request for race summary info:");
	         console.log("       Status: " + xhr.status + " - " + xhr.statusText);
	         console.log("Response Text: " + xhr.responseText);
	     }
	  });
}

function getDomainNames(request, response) {
	domain = this.element.attr('id');
  $.ajax({
     url: "getHisInfo.php",
     method: 'GET',
     dataType: 'json',
     data: {
      type: "autocomplete",
      name: request.term,
      domain: domain
     },
     success: function( data ) {
         //console.log(data);
         response( data );
     }
  });
}
// used for common entity like fields to use jquery UI autocomplete component
function acDomainFields(selector) {
	$(selector).autocomplete({
		delay: 500,
        source: getDomainNames,
        response: function( event,ui ) {
            if ( ui.content.length == 1 ) {
          	ui.item = ui.content[0];
          	$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
            }
        },
        select: function( event,ui ) {
      	  $("#" + $( this ).attr( 'id' )).val( ui.item.value );
      	  $(this).autocomplete( 'close' );
      	  var inputs = $(this).closest( 'form' ).find( ':input' );
          inputs.eq( inputs.index( this ) + 1 ).focus();
        },
        minLength: 1
      });
}

function setupCommonFields() {
  // populate autocomplete fields
  acDomainFields('#horse, #trainer, #jockey, #race_class, #race_flow, #previous_track_id');
 
  $('#race_date').datepicker({
	    currentText: 'Today',
	    defaultDate: 0,
	    dateFormat: 'yy-mm-dd',
	    showButtonPanel: true
  });

  $('#previous_date').datepicker({
	    currentText: 'Today',
	    defaultDate: 0,
	    dateFormat: 'yy-mm-dd',
	    showButtonPanel: true,
	    onSelect: function(race_date) {
	        getTrackId(race_date, '#previous_track_id', '#previous_race');
	    }
  });

}

function getTrackId(race_date, trackField, raceField) {
    // make ajax call if race_date is set and track_id 'field' is blank
    if (race_date != "" && $(trackField).val() == "") {
        
      // build query info for GET
      var queryData = new Object();
      queryData.type = 'get_track_id';
      queryData.race_date = race_date;
    
      // build settings/options for $.ajax call
      var options = new Object();
      options.data = queryData;
      options.dataType = "json";
      options.method = "GET";
      options.success = function(response, status, xhr) {
        $(trackField).val(response.track_id);
        $(trackField).trigger('change');
        $(raceField).focus();
      }
      options.error = function(xhr, status, errorThrown) {
        console.log("An error has occcured in 'getTrackId' function:");
        console.log("       Status: " + xhr.status + " - " + xhr.statusText);
        console.log("Response Text: " + xhr.responseText);
      }
      options.url = "getHisInfo.php";
    
      $.ajax(options);
    }
 }


function previous_trigger() {
    nextOutWinnersTable($("#previous_date").val(),
    		            $("#previous_race").val(),
    		            $("#previous_track_id").val());
}

function nextOutWinnersTable(race_date,race, track_id) {
    // make ajax call if all 'previous fields are filled in (except 'finish')
    if (race_date != "" && race > "0" && track_id != "") {
    
      // build query info for GET
      var queryData = new Object();
      queryData.type = 'next_out_winners';
      queryData.race_date = race_date;
      queryData.race = race;
      queryData.track_id = track_id;
    
      // build settings/options for $.ajax call
      var options = new Object();
      options.data = queryData;
      options.dataType = "json";
      options.method = "GET";
      options.success = function(response, status, xhr) {
    	$("#nextOutWinners").css('visibility', 'visible');
        $("#nextOutWinners").html(response.html);
        $('#nowTable').tablesorter({widgets: ['zebra']});
      }
      options.error = function(xhr, status, errorThrown) {
        console.log("An error has occcured in 'getNextOutWinners' function:");
        console.log("       Status: " + xhr.status + " - " + xhr.statusText);
        console.log("Response Text: " + xhr.responseText);
      }
      options.url = "getHisInfo.php";
    
      $.ajax(options);
    } else {
    	$("#nextOutWinners").css('visibility', 'hidden');
    }
 }

