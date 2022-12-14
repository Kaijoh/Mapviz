@extends('layout')

@section('main-section')

    <div class="container">
        <a href="/logout">Logout</a>
        <h2>Report Details</h2>
        <form actions="{{ route('addReport') }}" method="POST">
            @csrf
            
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">

            <div class="row">
                <div class="col-sm-3">
                    <label>Location</label><br>
                    <input type="text" name="location" id="location" required>
                </div>
                <div class="col-sm-3">
                    <label>Name</label><br>
                    <input type="text" name="name" placeholder="Name" required>
                </div>
                <div class="col-sm-3">
                    <label>Date</label><br>
                    <input type="date" name="date" required><br>
                    <span>Ex. 05/28/2019</span>
                </div>
            </div>
            <input type="text" id="latitude" name="latitude">
            <input type="text" id="longitude" name="longitude">
            <input type="text" id="ip" name="ip">
            <input type="text" id="city" name="city">
            <input type="text" id="dtime" name="dtime">
            <input type="text" id="dkm" name="dkm">
            <div class="row">
                <div class="col-sm">
                    <input type="submit" class="btn btn-primary">
                </div>
            </div>
        </form>
        @if(Session::has('success'))
            <p style="color:green;">{{ Session::get('success'); }}</p>
        @endif
    </div>

    <div class="container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody class="tbody">
                @if(count($reports) > 0)
                    @foreach($reports as $report)
                    <tr onclick="showMap({{ $report->latitude }}, {{ $report->longitude }})">
                        <td>{{ $report->id }}</td>
                        <td>{{ $report->name }}</td>
                        <td>{{ $report->location }}</td>
                        <td>{{ $report->latitude }}</td>
                        <td>{{ $report->longitude }}</td>
                        <td>{{ $report->date }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9"> No Reports!</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- google map html -->
    <div class="container mb-5">
        </div id="map" style="width:100%;height:300px;"></div>
    </div>

    <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBY5p5e5PtJuJLl_nRpjefL0S094jdhEP8&libraries=places"></script>-->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCpITCZus5GDSeqAO0guUi7Mc80BWGSpV4&libraries=places"></script>
    <!--<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyCdGv5cjpA0dMUCSolCf89tl_vgccGvsu0"></script>-->


    <script>
        $(document).ready(function(){
            var autocomplete;
            var to = 'location';
            autocomplete = new google.maps.places.Autocomplete((document.getElementById(to)),{
                types:['geocode'],
            });

            google.maps.event.addListener(autocomplete,'place_changed',function(){

                var near_place = autocomplete.getPlace();

                jQuery("#latitude").val( near_place.geometry.location.lat() );
                jQuery("#longitude").val( near_place.geometry.location.lng() );

                $.getJSON("https://api.ipify.org/?format=json",function(data){

                    let ip = data.ip;
                    jQuery("#ip").val(ip);
                    getCity(ip);
                });

            });
        });

        function getCity(ip){
            var req = new XMLHttpRequest();
            req.open("GET","http://ip-api.com/json/"+ip,true);
            req.send();

            req.onreadystatechange = function(){
                if(req.readyState == 4 && req.status ==200){
                    var obj = JSON.parse(req.responseText);
                    jQuery("#city").val(obj.city);
                    calculateDistance();
                }
            }
        }

        function calculateDistance(){

            var to = jQuery("#city").val();
            var from = jQuery("#location").val();
            
            var service = new google.maps.DistanceMatrixService();

            service.getDistanceMatrix({

                origins:[to],
                destinations:[from],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.metric,
                avoidHighways:false,
                avoidTolls: false

            },callback);

        }

        function callback(response,status)
        {
            if(status != google.maps.DistanceMatrixStatus.OK)
            {
                console.log("Something wrong");
            }
            else{

                if(response.rows[0].elements[0].status == "ZERO_RESULTS"){
                    console.log("no roads");
                }
                else{
                    var distance = response.rows[0].elements[0].distance;
                    var duration = response.rows[0].elements[0].duration;
                    var distance_in_km = distance.value/1000; 
                    var duration_in_minute = duration.value/60;
                    jQuery("#dkm").val(parseInt(distance.value/1000));
                    jQuery("#dtime").val(parseInt(duration_in_minute));
                }

            }
        }

        // map
        function showMap(lat,long)
        {
            var coord = { lat:lat, lng:long };

            var map = new google.maps.Map(
                document.getElementById("map"),
                {
                    zoom: 10,
                    center: coord
                }
            
            );

            new google.maps.Marker({
                position:coord,
                map:map
            });
        }

        showMap(0,0);
    </script>

@endsection