@extends('layouts.main')

@section('container')
<div class="container mt-4">
    <div id="message-container"></div>
    
    <form id="fwiForm">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="provinsi">Provinsi</label>
                <select class="form-control" id="provinsi" name="provinsi" required>
                    <option value="" selected>-- Provinsi --</option>
                    @foreach ($provinces as $provinsi)
                        <option value="{{ $provinsi->id }}">
                            {{ $provinsi->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="kabupaten">Kabupaten/Kota</label>
                <select class="form-control" id="kabupaten" name="kabupaten">
                    <option value="" selected>-- Kabupaten/Kota --</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="start_date">Tanggal Awal:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="form-group col-md-6">
                <label for="end_date">Tanggal Akhir:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ now()->addDays(7)->format('Y-m-d') }}">
            </div>
        </div>
        <div class="form-row mb-3 mt-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-block" id="fetchFWIDataButton">Submit</button>
            </div>
        </div>
    </form>

    <div id="map"></div>
    <div class="row">
        <canvas id="ffmcChart" width="800" height="200"></canvas>
    </div>
    <div class="row">
        <canvas id="dmcChart" width="800" height="200"></canvas>
    </div>
    <div class="row">
        <canvas id="dcChart" width="800" height="200"></canvas>
    </div>
    <div class="row">
        <canvas id="isiChart" width="800" height="200"></canvas>
    </div>
    <div class="row">
        <canvas id="buiChart" width="800" height="200"></canvas>
    </div>
    <div class="row">
        <canvas id="fwiChart" width="800" height="200"></canvas>
    </div>
    <div id="results"></div>
</div>

<style>
    #map { height: 500px; }
    .legend {
        line-height: 18px;
        color: #555;
        background: white;
        padding: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }
    .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
    }
    .alert {
        margin-top: 20px;
        padding: 10px;
        border-radius: 5px;
        position: relative;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }
    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 1.2em;
        cursor: pointer;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var geojsonLayer;
var geojsonData;
var colorMapping = {};
var savedColorMapping = {};
var map = L.map('map').setView([-1.269160, 117.825264], 5);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

function onMapClick(e) {
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + e.latlng.lat + '&lon=' + e.latlng.lng)
        .then(response => response.json())
        .then(data => {
            var address = data.address; 
            var province = address.state || address.city;
            var regency = address.city || address.town || address.county || address.village;
            var popupContent = "<strong>Province: </strong>" + province + "<br>" +
                               "<strong>Regency: </strong> " + regency + "<br>" +
                               "<a href='https://www.google.com/maps?q=" + e.latlng.lat + "," + e.latlng.lng + "' target='_blank'>See on Google Maps</a>";

            var popup = L.popup()
                .setLatLng(e.latlng)
                .setContent(popupContent)
                .openOn(map);
        })
        .catch(error => console.error('Error:', error));
}

map.on('click', onMapClick);

var charts = {};

document.addEventListener('DOMContentLoaded', function() {
    loadMapData();

    document.getElementById('provinsi').addEventListener('change', function() {
        saveCurrentColorMapping();
        if (this.value) {
            zoomToSelectedArea('provinsi', this.value);
        } else {
            loadAllData();
        }
    });

    document.getElementById('kabupaten').addEventListener('change', function() {
        saveCurrentColorMapping();
        if (this.value) {
            zoomToSelectedArea('kabupaten', this.options[this.selectedIndex].text);
        } else {
            var selectedProvinsi = document.getElementById('provinsi').value;
            if (selectedProvinsi) {
                zoomToSelectedArea('provinsi', selectedProvinsi);
            } else {
                loadAllData();
            }
        }
    });

    document.getElementById('fwiForm').addEventListener('submit', function(event) {
        event.preventDefault();
        fetchFWIData();
    });
});

function loadMapData() {
    loadAllData();
}

function loadAllData() {
    var geojsonPath = "/geojson/alldata.geojson";

    fetch(geojsonPath)
        .then(res => res.json())
        .then(data => {
            geojsonData = data;
            if (geojsonLayer) {
                map.removeLayer(geojsonLayer);
            }
            geojsonLayer = L.geoJson(data, {
                style: {
                    weight: 1
                }
            }).addTo(map);

            applyColorMapping();
            map.fitBounds(geojsonLayer.getBounds());
            fetchFWIDataCurrent();
        });
}

function fetchFWIDataCurrent() {
    fetch('https://forestfirepredictionidn.cloud/predict/api/fwi-data-current', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ date: new Date().toISOString().slice(0, 10) }),
    })
    .then(response => response.json())
    .then(data => {
        if (data) {
            applyFetchedColorMapping(data);
        } else {
            showAlert('No data found', 'alert-error');
        }
    })
    .catch(error => {
        showAlert('An error occurred while fetching the data.', 'alert-error');
    });
}

function fetchFWIData() {
    var provinsi = document.getElementById('provinsi').value;
    var kabupaten = document.getElementById('kabupaten').value;
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;

    fetch('https://forestfirepredictionidn.cloud/predict/api/fwi-data-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ provinsi: provinsi, kabupaten: kabupaten, start_date: startDate, end_date: endDate }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            clearCharts();
            displayFWICharts(data.data);
            applyFetchedColorMapping(data.data);
            showAlert('Data fetched successfully.', 'alert-success');
        } else {
            clearCharts();
            showAlert(data.message, 'alert-error');
        }
    })
    .catch(error => {
        clearCharts();
        showAlert('An error occurred while fetching the data.', 'alert-error');
    });
}

function clearCharts() {
    if (charts.ffmcChart) charts.ffmcChart.destroy();
    if (charts.dmcChart) charts.dmcChart.destroy();
    if (charts.dcChart) charts.dcChart.destroy();
    if (charts.isiChart) charts.isiChart.destroy();
    if (charts.buiChart) charts.buiChart.destroy();
    if (charts.fwiChart) charts.fwiChart.destroy();

    charts = {};
}

function displayFWICharts(data) {
    var labels = data.map(entry => entry.date);
    var ffmc = data.map(entry => entry.FFMC);
    var dmc = data.map(entry => entry.DMC);
    var dc = data.map(entry => entry.DC);
    var isi = data.map(entry => entry.ISI);
    var bui = data.map(entry => entry.BUI);
    var fwi = data.map(entry => entry.FWI);

    var ctxFFMC = document.getElementById('ffmcChart').getContext('2d');
    var ctxDMC = document.getElementById('dmcChart').getContext('2d');
    var ctxDC = document.getElementById('dcChart').getContext('2d');
    var ctxISI = document.getElementById('isiChart').getContext('2d');
    var ctxBUI = document.getElementById('buiChart').getContext('2d');
    var ctxFWI = document.getElementById('fwiChart').getContext('2d');

    charts.ffmcChart = new Chart(ctxFFMC, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'FFMC',
                data: ffmc,
                borderColor: '#FF4560',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'FFMC Data'
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Index'
                    }
                }
            }
        }
    });

    charts.dmcChart = new Chart(ctxDMC, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'DMC',
                data: dmc,
                borderColor: '#00E396',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'DMC Data'
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Index'
                    }
                }
            }
        }
    });

    charts.dcChart = new Chart(ctxDC, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'DC',
                data: dc,
                borderColor: '#FEB019',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'DC Data'
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Index'
                    }
                }
            }
        }
    });

    charts.isiChart = new Chart(ctxISI, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'ISI',
                data: isi,
                borderColor: '#775DD0',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'ISI Data'
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Index'
                    }
                }
            }
        }
    });

    charts.buiChart = new Chart(ctxBUI, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'BUI',
                data: bui,
                borderColor: '#546E7A',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'BUI Data'
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Index'
                    }
                }
            }
        }
    });

    charts.fwiChart = new Chart(ctxFWI, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'FWI',
                data: fwi,
                borderColor: '#26a69a',
                fill: false
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'FWI Data'
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Index'
                    }
                }
            }
        }
    });
}

function saveCurrentColorMapping() {
    savedColorMapping = { ...colorMapping };
}

function restoreSavedColorMapping() {
    colorMapping = { ...savedColorMapping };
    applyColorMapping();
}

function applyColorMapping() {
    if (geojsonLayer && colorMapping) {
        geojsonLayer.setStyle(function (feature) {
            var altName = feature.properties.alt_name;

            if (colorMapping.hasOwnProperty(altName)) {
                return {
                    fillColor: colorMapping[altName],
                    weight: 2,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: 0.7
                };
            } else {
                return {
                    fillColor: 'gray',
                    weight: 1,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: 0.7
                };
            }
        });
    }
}

function applyFetchedColorMapping(data) {
    data.forEach(entry => {
        colorMapping[entry.name] = getColor(entry.FWI);
    });
    applyColorMapping();
}

function zoomToSelectedArea(type, id) {
    var filteredData;
    if (type === 'provinsi') {
        filteredData = geojsonData.features.filter(function(feature) {
            return feature.properties.prov_id === id;
        });
    } else if (type === 'kabupaten') {
        filteredData = geojsonData.features.filter(function(feature) {
            return feature.properties.alt_name.toLowerCase() === id.toLowerCase();
        });

        if (filteredData.length === 0) {
            var selectedProvinsi = document.getElementById('provinsi').value;
            if (selectedProvinsi) {
                filteredData = geojsonData.features.filter(function(feature) {
                    return feature.properties.prov_id === selectedProvinsi;
                });
            }
        }
    }

    if (geojsonLayer) {
        map.removeLayer(geojsonLayer);
    }

    geojsonLayer = L.geoJson(filteredData, {
        style: {
            weight: 1
        }
    }).addTo(map);

    restoreSavedColorMapping();
    map.fitBounds(geojsonLayer.getBounds());
}

var legend = L.control({position: 'bottomright'});

legend.onAdd = function (map) {
    var div = L.DomUtil.create('div', 'legend'),
        grades = [0, 1, 6, 13],
        labels = [];

    div.innerHTML += '<strong>FWI Index</strong><br>';
    div.innerHTML += '<i style="background:#A2A19F"></i> Tidak Ada Data<br>';
    div.innerHTML += '<i style="background:#0E7AD1"></i> Rendah (< 1)<br>';
    div.innerHTML += '<i style="background:#00FF00"></i> Sedang (1 - 6)<br>';
    div.innerHTML += '<i style="background:#FFFF00"></i> Tinggi (6 - 13)<br>';
    div.innerHTML += '<i style="background:#FF0000"></i> Sangat Tinggi (> 13)<br>';

    return div;
};

legend.addTo(map);

function getColor(d) {
    return d === null || d === undefined ? '#A2A19F' :
           d > 13 ? '#FF0000' :
           d > 6  ? '#FFFF00' :
           d > 1  ? '#00FF00' :
                    '#0E7AD1';
}



function showAlert(message, type) {
    var messageContainer = document.getElementById('message-container');
    messageContainer.innerHTML = '';

    var alertElement = document.createElement('div');
    alertElement.className = 'alert ' + type;
    alertElement.role = 'alert';

    var closeButton = document.createElement('button');
    closeButton.className = 'close';
    closeButton.innerHTML = '&times;';
    closeButton.onclick = function() {
        messageContainer.innerHTML = '';
    };

    alertElement.innerText = message;
    alertElement.appendChild(closeButton);

    messageContainer.appendChild(alertElement);
}
</script>

@endsection
