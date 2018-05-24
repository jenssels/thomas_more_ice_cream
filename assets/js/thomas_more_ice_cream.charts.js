(function($, drupalSettings){
  $(document).ready(function(){
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      console.log(drupalSettings.chart_smaken);
      var data = google.visualization.arrayToDataTable(drupalSettings.chart_smaken
      );

      var options = {
        title: 'Smaken ijsjes',
        is3D: true
      };

      var chart = new google.visualization.PieChart(document.getElementById('ijs'));

      chart.draw(data, options);
    }
  });

})(jQuery, drupalSettings);