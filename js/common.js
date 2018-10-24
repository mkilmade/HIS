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
function previous_trigger() {
    var previous_date=$("#previous_date").val(),
        previous_track_id=$("#previous_track_id").val(),
        previous_race=$("#previous_race").val();

    // make ajax call if all 'previous fields are filled in (except 'finish')
    if (previous_date != "" && previous_track_id != "" && previous_race != "") {

      // build query info for GET
      var queryData = new Object();
      queryData.type = 'previous_next_out_winners';
      queryData.previous_date = previous_date;
      queryData.previous_track_id = previous_track_id;
      queryData.previous_race = previous_race;

      // build settings/options for $.ajax call
      var options = new Object();
      options.data = queryData;
      options.dataType = "json";
      options.method = "GET";
      options.success = function(response, status, xhr) {
        $("#winnersOutOfPrevious").html(" (Winners out of previous race: " + response.wins + ")");
      }
      options.error = function(xhr, status, errorThrown) {
        console.log("An error has occcured in 'previous_trigger' function:");
        console.log("       Status: " + xhr.status + " - " + xhr.statusText);
        console.log("Response Text: " + xhr.responseText);
      }
      options.url = "getHisInfo.php";

      $.ajax(options);
    } else {
    	$("#winnersOutOfPrevious").html(" (Winners out of previous race: tbd)");
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
    }
    options.error = function(xhr, status, errorThrown) {
      console.log("An error has occcured in request for next race #:");
      console.log("       Status: " + xhr.status + " - " + xhr.statusText);
      console.log("Response Text: " + xhr.responseText);
    }
    options.url = "getHisInfo.php";

    $.ajax(options);	
}

function getDomainNames(request, response) {
  $.ajax({
     url: "getHisInfo.php",
     method: 'GET',
     dataType: 'json',
     data: {
      type: "autocomplete",
      name: request.term,
      domain: this.element.attr('id')
     },
     success: function( data ) {
         console.log(data);
         response( data );
     }
  });
}

function setupCommonFields() {
  // populate autocomplete lists options
  $('#horse, #trainer, #jockey, #race_class, #race_flow').autocomplete({
          source: getDomainNames,
       minLength: 1
  });
 
  $('#race_date, #previous_date').datepicker({
    currentText: 'Today',
    defaultDate: 0,
    dateFormat: 'yy-mm-dd',
    showButtonPanel: true
  });

}

function getTrackId(race_date) {
    var race_datex = $("#race_date").val(),
        track_id  = $("#track_id").val();

    // make ajax call if all 'previous fields are filled in (except 'finish')
    if (race_date != "" && track_id == "") {
        
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
        console.log("Response Text: " + xhr.responseText);
        $("#track_id").val(response.track_id);
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

