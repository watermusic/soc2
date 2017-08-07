import axios from 'axios'
import _ from 'underscore'
import Mustache from 'mustache'
import toastr from 'toastr'
import swal from 'sweetalert2'

let SOC = {};

$(function() {

  SOC.LineupStorage = (function () {

    let
      CONST_TORWART       = 'Torwart',
      CONST_ABWEHR        = 'Abwehr',
      CONST_MITTELFELD    = 'Mittelfeld',
      CONST_STURM         = 'Sturm',

      config = $("#config"),
      user = config.data('user'),

      that = this,

      storage = {},
      limits = {};

    limits[CONST_TORWART] = 1;
    limits[CONST_ABWEHR] = 3;
    limits[CONST_MITTELFELD] = 5;
    limits[CONST_STURM] = 2;

    let isSpace = function(position) {
      let n = limits[position];
      return storage[position].length < n;
    };

    return {

      init: function () {
        that = this;
        that.reset();
      },
      hasSpace: function() {
        return _.without(SOC.LineupStorage.getFlatten(), null).length == 11;
      },
      add: function(position, playerId) {
        if(isSpace(position) === false) {
          return -1;
        }

        if(_.contains(storage[position], playerId)) {
          return false;
        }
        storage[position].push(playerId);
        return storage;
      },
      remove: function(playerId) {
        let position = null;
        _.each(storage, function(pos,positionName){
          _.each(pos, function(id) {
            if(id == playerId) {
              position = positionName;
            }
          });
        });

        if(position === null) {
          return;
        }

        storage[position] = _.without(storage[position], playerId);
        return storage;
      },
      has: function(position, playerId) {
        return _.contains(storage[position], playerId);
      },
      get: function() {
        return storage;
      },
      getFlatten : function() {
        let
          result = [],
          m = 0;
        _.each(limits, function(limit, position) {
          for(let n = 0; n < limit; n++) {
            result[m] = (_.isUndefined(storage[position][n])) ? null : storage[position][n];
            m++;
          }
        });
        return result;
      },
      set: function(lineup) {
        storage = lineup;
        return storage;
      },
      reset: function() {
        storage[CONST_TORWART] = [];
        storage[CONST_ABWEHR] = [];
        storage[CONST_MITTELFELD] = [];
        storage[CONST_STURM] = [];
      },
      save: function() {
        let uri = Routing.generate('soc_rest_lineup_post', { username: user.username });
        let data = {
          matchday: SOC.BenchCtrl.getMatchday(),
          data: {
            lineup: that.get()
          }
        };
        return axios.post(uri, data);
      },
      read: function(num) {
        let uri = Routing.generate('soc_rest_lineup_get', { username: user.username, id: num });
        return axios.get(uri);
      },
      update: function() {

      }
    };

  })();

  SOC.LineupCtrl = (function () {

    let field = $('.field'),
      template = $('#lineup-item').html();


    return {

      init: function () {

        field.on('click', '.pi-lineup .exchange a', function() {
          let lineupItem = $(this).parent().parent().parent().parent(),
            playerId = lineupItem.data('ref');
          SOC.LineupStorage.remove(playerId);
          SOC.LineupCtrl.render();
          SOC.BenchCtrl.showPlayer(playerId);
        });

      },
      render: function() {

        field.empty();
        let lineup = SOC.LineupStorage.getFlatten();
        _.each(lineup, function(num, index) {

          if(num === null) {
            return;
          }

          let playerData = SOC.BenchCtrl.getPlayerData(num);
          if(_.isUndefined(playerData)) {
            throw 'There is no player with the id' + num + ' on the bench';
          }
          playerData.index = index + 1;
          playerData.team.lowerName = playerData.team.name.toLowerCase();

          let rendered = Mustache.render(template, playerData);

          field.append(rendered);


        });

      }

    };

  })();

  SOC.BenchCtrl = (function () {

    let bench = $('.bench'),
      btnsAddPlayer = $('.bench-item .exchange a', bench),
      ddMatchday = $('#ddMatchday'),
      btnSave = $('.btn-save', bench),
      btnPrint = $('.btn-print', bench),
      btnReset = $('.btn-reset', bench),
      that = this
    ;

    let getPlayerElement = function(playerId) {
      return $("div[data-ref='" + playerId + "']", bench);
    };

    return {

      init: function () {

        that = this;

        btnsAddPlayer.on('click', function() {

          let playerData = $(this).parent().parent().parent().parent().data('value'),
            position = $(this).data('position');

          let result = SOC.LineupStorage.add(position, playerData.id);
          if(_.isObject(result) === false) {
            toastr.warning('Das Spielfeld ist schon voll oder der Spieler steht schon auf dem Platz!', 'Achtung');
            return;
          }
          SOC.LineupCtrl.render();
          SOC.BenchCtrl.hidePlayer(playerData.id);

        });

//                        btnSave
//                        btnPrint

        btnReset.on('click', function() {
          swal({
            title: "Bist du sicher?",
            text: "Möchtest du alle Spieler vom Platz nehmen?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ja, bitte vom Platz nehmen!"
          }).then(function () {
            swal("Glückwunsch!", "Du hast alle Spieler vom Platz genommen.", "success");
            SOC.LineupStorage.reset();
            SOC.LineupCtrl.render();
            that.showAll();
          });
        });

        btnPrint.on('click', function() {
          let url = Routing.generate('soc_lineup_print', {'matchday' : 'NUM', '_format' : 'pdf'});
          url = url.replace("NUM", that.getMatchday());
          window.open(url);
        });

        btnSave.on('click', function() {

          if(SOC.LineupStorage.hasSpace() === false) {
            toastr.error('Du musst eine vollständige Mannschaft aufstellen.', 'Fehler');
            return;
          }

          let request = SOC.LineupStorage.save();
          request.then(function() {
            toastr.success('Deine Aufstellung für den ' + that.getMatchday() + ' . Spieltag wurde gespeichert!', 'Super');
          });
          request.catch(function() {
            toastr.error('Beim Speichern traten Probleme auf! Bitte Aufstellung zurücksetzen und noch einmal probieren', 'Fehler');
          });

        });

        ddMatchday.on('change', function() {

          let request = SOC.LineupStorage.read(that.getMatchday());
          request.then(function (result) {

            let lineup = result.data.data.lineup;

            _.each(lineup, function(v,k) {
              lineup[k] = _.map(v, function(num) { return parseInt(num, 10) });
            });

            SOC.LineupStorage.reset();
            SOC.LineupStorage.set(lineup);
            let flatten = SOC.LineupStorage.getFlatten();
            _.each(flatten, function(id) {
              that.hidePlayer(id);
            });
            SOC.LineupCtrl.render();
          });
          request.catch(function () {
            SOC.LineupStorage.reset();
            SOC.LineupCtrl.render();
            that.showAll();
            toastr.warning('Für diesen Spieltag liegt noch keine Aufstellung vor!', 'Achtung');
          });

        });

        _.delay(function() {
          ddMatchday.trigger('change');
        }, 1000);

      },
      getMatchday: function() {
        return ddMatchday.val();
      },
      getPlayerData: function(playerId) {
        return getPlayerElement(playerId).data('value');
      },
      showPlayer: function(playerId) {
        return getPlayerElement(playerId).show(400);
      },
      showAll: function() {
        let players = $(".bench-item .pi", bench);
        _.each(players, function(element) {
          $(element).show(400);
        });
      },
      hidePlayer: function(playerId) {
        return getPlayerElement(playerId).hide(400);
      }

    };

  })();

  SOC.MainCtrl = (function () {

    return {
      init: function () {

        toastr.options = {
          "closeButton": true,
          "debug": false,
          "progressBar": true,
          "positionClass": "toast-top-center",
          "onclick": null,
          "showDuration": "400",
          "hideDuration": "1000",
          "timeOut": "7000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        };

        SOC.LineupStorage.init();
        SOC.BenchCtrl.init();
        SOC.LineupCtrl.init();
      }
    };

  })();
  SOC.MainCtrl.init();

});