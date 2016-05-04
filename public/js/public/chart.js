function initLineChart(lineId, dataLabels, dataValues, lineOption) {
    if (typeof dataValues != 'undefined' && dataValues.length != 0) {
        var lineData = {
            labels: dataLabels,
            datasets: [
                {
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: dataValues
                }
            ]
        };
        if (typeof lineOption == 'undefined') {
            lineOption = {
                responsive: true
            };
        }

        var wChart = 'chart' + lineId;
        if (typeof window[wChart] != 'undefined') {
            window[wChart].destroy();
        }
        var chartLineCanvas = document.getElementById(lineId).getContext("2d");
        window[wChart] = new Chart(chartLineCanvas).Line(lineData, lineOption);
    } else {
        alert('No data.');
    }
}

function initBarChart(barId, dataLabels, dataValues) {
    if (typeof dataValues != 'undefined' && dataValues.length > 0) {
        var barData = {
            labels: dataLabels,
            datasets: [
                {
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: dataValues
                }
            ]
        };
        var barOption = {
            responsive: true
        };

        var wChart = 'chart' + barId;
        if (typeof window[wChart] != 'undefined') {
            window[wChart].destroy();
        }
        //$('#' + doughnutId).get(0).getContent('2d');
        var chartBarCanvas = document.getElementById(barId).getContext("2d");
        window[wChart] = new Chart(chartBarCanvas).Bar(barData, barOption);
    } else {
        alert('No data.');
    }
}

function initPieChart(pieId, dataLabels, dataValues) {
    if (typeof dataValues != 'undefined' && dataValues.length > 0) {
        var pieData = [];
        var hexColor = '';
        for (var i = 0, len = dataLabels.length; i < len; i++) {
            hexColor = getRandomColorHex();
            pieData.push(
                {
                    value: Number(dataValues[i]), //required, number, string failed
                    color: hexColor, //required
                    highlight: hexColor, //optional
                    label: dataLabels[i] //optional
                }
            );
        }
        var pieOption = {
            responsive: true
        };

        var wChart = 'chart' + pieId;
        if (typeof window[wChart] != 'undefined') {
            window[wChart].destroy();
        }
        //$('#' + pieId).get(0).getContent('2d');
        var chartPieCanvas = document.getElementById(pieId).getContext("2d");
        window[wChart] = new Chart(chartPieCanvas).Pie(pieData, pieOption);
    } else {
        alert('No data.');
    }
}

function initDoughnut(doughnutId, dataLabels, dataValues) {
    if (typeof dataValues != 'undefined' && dataValues.length > 0) {
        var doughnutData = [];
        var hexColor = '';
        for (var i = 0, len = dataLabels.length; i < len; i++) {
            hexColor = getRandomColorHex();
            doughnutData.push(
                {
                    value: Number(dataValues[i]), //required, number, string failed
                    color: hexColor, //required
                    highlight: hexColor, //optional
                    label: dataLabels[i] //optional
                }
            );
        }
        var doughnutOption = {
            responsive: true
        };

        var wChart = 'chart' + doughnutId;
        if (typeof window[wChart] != 'undefined') {
            window[wChart].destroy();
        }
        //$('#' + doughnutId).get(0).getContent('2d');
        var chartDoughnutCanvas = document.getElementById(doughnutId).getContext("2d");
        window[wChart] = new Chart(chartDoughnutCanvas).Doughnut(doughnutData, doughnutOption);
    } else {
        alert('No data.');
    }
}

function getRandomColorHex() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}