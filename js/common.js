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
      queryData.horse= horse;

      // build settings/options for $.ajax call
      var options = new Object();
      options.data = queryData;
      options.dataType = "json";
      options.method = "GET";
      options.success = function(lastWinData, status, xhr) {
        $("#trainer").val(lastWinData.trainer);
        $("#jockey").val(lastWinData.jockey);
      }
      options.error = function(xhr, status, errorThrown) {
        console.log("An error has occcured in 'horse_trigger' function:");
        console.log("       Status: " + xhr.status + " - " + xhr.statusText);
        console.log("Response Text: " + xhr.responseText);
      }
      options.url = "getLastWinData.php";

      $.ajax(options);
    }
}

// table entity autocmplete functions
function getHorses(request, response) {
  getNames(request, response, "horse", "getEntityNames.php");
}
function getTrainers(request, response) {
  getNames(request, response, "trainer", "getEntityNames.php");
}
function getJockeys(request, response) {
  getNames(request, response, "jockey", "getEntityNames.php");
}
// catgegory autocomplete functions
function getRaceClasses(request, response) {
  getNames(request, response, "race_class", "getCategoryNames.php");
}
function getRaceFlows(request, response) {
  getNames(request, response, "race_flow", "getCategoryNames.php");
}

function getNames(request, response, entity_name, url) {
  $.ajax({
     url: url,
     method: 'GET',
     dataType: 'json',
     data: {
      name: request.term,
      entity_name: entity_name
     },
     success: function( data ) {
         console.log(data);
         response( data );
     }
  });
}
function setupCommonFields() {
  // populate autocomplete lists options
  $('#horse').autocomplete({
          source: getHorses,
       minLength: 1
  });
  $('#trainer').autocomplete({
         source: getTrainers,
      minLength: 1
  });
  $('#jockey').autocomplete({
         source: getJockeys,
      minLength: 1
  });
  $('#race_class').autocomplete({
         source: getRaceClasses,
      minLength: 1
  });
  $('#race_flow').autocomplete({
         source: getRaceFlows,
      minLength: 0
  });

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
    showButtonPanel: true
  });

}
