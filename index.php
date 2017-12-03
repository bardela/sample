<?php
require('src/autoload.php');
?>

<html>
  <head>
    <title>Geolocation</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.2/vue.js"></script>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.1.10/vue-resource.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
  </head>

  <body>
    <div class="container">
      <h1 class="title">Tweeds about</h1>
      <div id="map_container"></div>

        <div id="map"></div>
        <!-- load googlemap -->
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmapKey?>&callback=initMap&libraries=places">
        </script>
        <!-- load googlemap -->
        <div class="clear"></div>
        <div class="row" id="maptweeds">
          <div class="cell">
          <form method="POST" v-on:submit="onSubmitForm">
          
            <input v-bind:value="searchv" type="text" name="name" id="name" class="form-control bar" placeholder="city name">
            <button type="submit" class="btn btn-default bar blue">
                      SEARCH
            </button>
          </form>
          </div>
          <div class="cell bar blue">
              <div class="mibutton" id="history_button" v-on:click="showHistory">
                <div class="middle">History</div>
              </div>
              <div id="history_list" class="history_list" style="display:none">
                <ul>
                  <li v-for="search in searches" v-text="search" v-on:click="(event) => {repeatSearch(event, search)}"></li>
                <!--
                    <li>Madrid</li>
                    <li>Barcelona</li>
                    <li>Bangkok</li>
                -->
                </ul>
              </div>

          </div>
          <!-- -->
        </div><!--row -->
      <script type="text/javascript" src="js/app.js"></script>

    </div>
</body>
</html>