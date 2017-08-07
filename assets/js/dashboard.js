import * as moment from 'moment';
import 'moment/min/locales.min';
import Mustache from 'mustache';
import axios from 'axios';
import _ from 'underscore';
import 'chart.js';

let SOC = {};

$(function() {

  SOC.NewsCtrl = (function () {

    let feed_url = Routing.generate('soc_dashboard_news'),
      viewCont = $('#news-timeline'),
      viewRefreshBtn = $('#news-refresh'),
      template = $('#news-timeline-item').html();

    let drawTimeline = function(items) {
      items = _.map(items.data, function(num) {
        num.date = moment(num.date).calendar();
        return num;
      });
      let rendered = Mustache.render(template, {'items' : items});
      viewCont.html(rendered);
    };

    return {
      init: function () {

        Mustache.parse(template);

        viewRefreshBtn.on('click', function() {
          SOC.NewsCtrl.refresh();
        });

        _.delay(SOC.NewsCtrl.refresh(), 500);

      },
      refresh : function() {

        viewCont.html('<br>');
        axios.get(feed_url)
          .then(function(res) {
            drawTimeline(res);
          })
          .catch(function() {});
      }
    };


  })();

  SOC.MatchdayCtrl = (function() {

    let
      lineOptions,
      lineData,
      pptMatchdayChart,
      config = $("#config"),
      user = config.data('user'),

      chartData = config.data('chart-data'),
      matchdays = config.data('matchdays'),

      ctx = document.getElementById("pptMatchday").getContext("2d");

    lineOptions = {
      scaleBeginAtZero: true,
      datasetFill: true,
      responsive: true
    };

    lineData = {
      labels: matchdays,
      datasets: [
        {
          label: user.username,
          fillColor: "rgba(220,220,220,0.5)",
          strokeColor: "rgba(220,220,220,1)",
          pointColor: "rgba(220,220,220,1)",
          pointStrokeColor: "#fff",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
          data: chartData[user.username]
        }
      ]
    };
    pptMatchdayChart = new Chart(ctx, {
      type: 'bar',
      data: lineData,
      options: lineOptions
    });

    return {

      init: function() {

      },
      refresh : function() {
        pptMatchdayChart.update();
      }

    };

  })();

  SOC.MainCtrl = (function () {

    return {
      init: function () {
        moment.locale('de');
        SOC.NewsCtrl.init();
      }
    };

  })();
  SOC.MainCtrl.init();

});
