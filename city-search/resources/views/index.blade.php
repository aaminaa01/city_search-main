<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Interface</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center; /* Center content horizontally */
            background-color: #ffffff; /* White background */
            color: #000000; /* Black text color */
            margin: 0; /* Remove default margin */
        }

        #cityList {
            width: 40%;
            margin-right: 10px;
            max-height: 500px; /* Set a fixed height */
            overflow-y: auto; /* Enable vertical scrollbar */
            background-color: #8a2be2; /* Purple background */
            color: #ffffff; /* White text color */
            padding: 10px;
            border-radius: 10px;
        }

        .cityButton {
            margin-bottom: 5px;
            background-color: #c8a2c8; /* Light Purple background for buttons */
            color: #000000; /* Black text color */
        }

        #closestCities {
            width: 40%;
            background-color: #000000; /* Black background */
            color: #ffffff; /* White text color */
            padding: 10px;
            border-radius: 10px;
        }

        #closestCitiesList li {
            background-color: #8a2be2; /* Purple background for list items */
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div id="cityList" class="m-3">
    <h3>Select a city:</h3>
    @foreach ($cities as $city)
        @if(!empty($city['city']))
            <button class="btn cityButton" loc-id="{{ $city['locId'] }}" data-lat="{{ $city['latitude'] }}" data-lon="{{ $city['longitude'] }}">
                {{ $city['city'] }}
            </button>
            <br>
        @endif
    @endforeach
</div>

<!-- New element to display the closest cities -->
<div id="closestCities" class="m-3">
    <h3 id="selectedCity">Selected City: </h3>
    <h3>5 Closest Cities:</h3>
    <h5>(this may take a moment)</h5>
    <ul id="closestCitiesList" class="list-unstyled"></ul>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add click event listeners to all buttons with the class 'cityButton'
    var cityButtons = document.querySelectorAll('.cityButton');
    cityButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedCityId = this.getAttribute('loc-id');
            const selectedLat = this.getAttribute('data-lat');
            const selectedLon = this.getAttribute('data-lon');

            // Update the selected city text
            const selectedCityText = document.getElementById('selectedCity');
            selectedCityText.innerText = 'Selected City: ' + this.innerText;

            fetch('/get-closest-cities/' + selectedCityId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);

                    // Display closest cities
                    const closestCitiesList = document.getElementById('closestCitiesList');
                    closestCitiesList.innerHTML = '';
                    // Use a valid JavaScript loop syntax
                    for (var i = 0; i < 5; i++) {
                        closestCitiesList.innerHTML += '<li>' + data[i].city+', '+data[i].country +'('+data[i].latitude+', '+data[i].longitude+')' +'</li>';

                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('closestCitiesList').innerHTML = '<li>' + 'BBB' + '</li>';
                });
        });
    });
});
</script>

</body>
</html>
