
var App = window.App = new Vue({
    el: '#maptweeds',

    data: {
        submitted: false,
        searches: [],
        searchv: null,
    },

    computed: {
        errors: function() {
            for (var key in this.newMessage) {
                if ( ! this.newMessage[key]) return true;
            }

            return false;
        }
    },

    methods: {
        fetchMessages: function() {
            this.$http.get('/api/messages', function(messages) {
                this.$set('messages', messages);
            });
        },

        onSubmitForm: function(e) {
            e.preventDefault();

            this.submitted = true;

            search  = $('#name').val();
            this.searches.push(search);
            this.searchTweeds(search);
            title   = $('.title').html('Tweeds about '+search);
            $('.title').show();
        },
        searchTweeds: function(search) {
            this.$http.get('controller.php?type=coordinates&search='+search, function(response1){
                place   = response1;
                //mv map
                map.panTo(new google.maps.LatLng(place.coordinates.lat, place.coordinates.lng));
                this.$http.get('controller.php?type=search&search='+search+'&lat='+place.coordinates.lat+'&long='+place.coordinates.lng, function(response2){
                    var tweedsData  = response2.statuses;
                    var tweeds = new Array();

                    for (var i=0; i<tweedsData.length; i++) {
                        tweed = new Object();
                        tweed.text      = tweedsData[i].text;
                        tweed.userId    = tweedsData[i].user.id;
                        tweed.userName  = tweedsData[i].user.name;
                        tweed.image     = tweedsData[i].user.profile_image_url;
                        //default lat & long
                        signed  = tweedsData.length % 2 == 0 ? '1': '-1';
                        offset     = i * 0.00005 * tweedsData.length * signed;
                        tweed.lat   = offset + place.coordinates.lat;
                        tweed.lng  = offset + place.coordinates.lng;
                        if (tweedsData[i].coordinates !== null){
                            if (typeof tweedsData[i].coordinates.coordinates !== 'undefined') {
                                tweed.lat       = tweedsData[i].coordinates.coordinates[1];
                                tweed.lng       = tweedsData[i].coordinates.coordinates[0];
                            }
                        }
                        addMarker(tweed);
                        tweeds.push(tweed);
                    }
                }, place);
            }, search);

        },
        showHistory: function(e) {
            e.preventDefault();
            var history = $('#history_list');
            if (!history.is(':visible')){
                history.show();
            } else {
                history.hide();
            }
        },
        repeatSearch: function(e, search) {
            e.preventDefault();
            this.searchv = search;
            this.searchTweeds(search);
            title   = $('.title').html('Tweeds about '+search);
            $('.title').show();
        }
    }
});



function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 0, lng: 0},
        zoom: 13,
        mapTypeId: 'roadmap'
    });

    infoWindow = new google.maps.InfoWindow;

    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        map.setCenter(pos);
      });
    }
}



function addMarker(tweed) {
    var marker = new google.maps.Marker({
      position: {lat: tweed.lat, lng: tweed.lng},
      icon: tweed.image,
      shape:{coords:[17,17,18],type:'circle'},
      optimized:false,
      map: map
    });
    marker.addListener('click', function() {
      infoWindow.setContent(tweed.text)
      infoWindow.open(map, marker);
    });
}
