<?php
$cpuUsageReporterCounter = 0;
function generateUsageStatisticsPlot($usageType, $latest) {
  global $cpuUsageReporterCounter;
?>

  <div id="xkcdplot"></div>
  <script src="<?php print get_stylesheet_directory_uri();?>/jquery.flot.js"></script>
  <script>
  jQuery(document).ready(function($) {
      // we use an inline data source in the example, usually data would
      // be fetched from a server
      var data = [], totalPoints = 300;

      var updateInterval = 1000;
      var timeLimit = 600;
      var isFirst = true;
      var options;
      var latest = "<?php print $latest; ?>";
      var usageType = "<?php print $usageType; ?>";
      // setup plot
      if(latest == 1) {
	  options = {
		    series: {
			    pie: { 
				    show: true
			    }
		    }
	  };
      } else {
	  options = {
	      series: { shadowSize: 0 }, // drawing is faster without shadows
	      yaxis: { min: 0, max: 100 },
	      xaxis: { min: -timeLimit, max: 0 },
	      legend: { show: true, position: "nw" } //container: "#xkcdlegend" }
	  };
      }
      var plot;
      function update() {
	  $.getJSON("/wp-content/plugins/cpu-reporter/results.php?timeLimit=" + timeLimit + "&type=" + usageType + "&latest=" + latest,  function(inData) {

	      var someData = [];
	      var counter = 0;
	      for(var i in inData) {
		  someData[counter] = {label: i, data: []};
		  var isActive = false;
		  for(var j in inData[i]) {
		      someData[counter].data.push([parseFloat(inData[i][j]["x"]), parseFloat(inData[i][j]["y"])]);
		      if(inData[i][j]["is_active"] > 0) {
			  isActive = true;
		      }
		  }
		  if(isActive) {
		      someData[counter].label += "*";
		  }
		  counter += 1;
	      }
	      if(isFirst || latest == 1) {
		  plot = $.plot($("#xkcdplot"), someData, options);
		  if(latest == 0) {
		      plot.draw();
		  }
		  isFirst = false;
	      } else {
		  plot.setData(someData);
		  // since the axes don't change, we don't need to call plot.setupGrid()
		  plot.draw();
	      }
	      setTimeout(update, updateInterval);
	  });
      }
      console.log("Updating");
      update();
  });
  </script>
<?php
  $cpuUsageReporterCounter++;
}
?>