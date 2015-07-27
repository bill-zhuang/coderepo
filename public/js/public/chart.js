function initLineChart(line_id, data_labels, data_values, line_option) {
    if (typeof data_values != 'undefined' && data_values.length != 0) {
        var line_data = {
            labels : data_labels,
            datasets: [
                {
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: data_values
                }
            ]
        };
        if (typeof line_option == 'undefined') {
            line_option = {
                responsive: true
            };
        }

        if (typeof window.line_chart  != 'undefined') {
            line_chart.destroy();
        }
        var chart_line_canvas = document.getElementById(line_id).getContext("2d");
        window.line_chart = new Chart(chart_line_canvas).Line(line_data, line_option);
    } else {
        alert('No data.');
    }
}

function initBarChart(doughnut_id, data_labels, data_values) {
    if (typeof data_values != 'undefined' && data_values.length > 0) {
        var bar_data = {
            labels : data_labels,
            datasets: [
                {
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: data_values
                }
            ]
        };
        var bar_option = {
            responsive: true
        };

        if (typeof window.bar_chart != 'undefined') {
            bar_chart.destroy();
        }
        //$('#' + doughnut_id).get(0).getContent('2d');
        var chart_bar_canvas = document.getElementById(doughnut_id).getContext("2d");
        window.bar_chart = new Chart(chart_bar_canvas).Bar(bar_data, bar_option);
    } else {
        alert('No data.');
    }
}

function initPieChart(pie_id, data_labels, data_values) {
    if (typeof data_values != 'undefined' && data_values.length > 0) {
        var pie_data = [];
        var hex_color = '';
        for(var i = 0, len = data_labels.length; i < len; i++) {
            hex_color = getRandomColorHex();
            pie_data.push(
                {
                    value: Number(data_values[i]), //required, number, string failed
                    color: hex_color, //required
                    highlight: hex_color, //optional
                    label: data_labels[i] //optional
                }
            );
        }
        var pie_option = {
            responsive: true
        };

        if (typeof window.pie_chart != 'undefined') {
            pie_chart.destroy();
        }
        //$('#' + pie_id).get(0).getContent('2d');
        var chart_pie_canvas = document.getElementById(pie_id).getContext("2d");
        window.pie_chart = new Chart(chart_pie_canvas).Pie(pie_data, pie_option);
    } else {
        alert('No data.');
    }
}

function initDoughnut(doughnut_id, data_labels, data_values) {
    if (typeof data_values != 'undefined' && data_values.length > 0) {
        var doughnut_data = [];
        var hex_color = '';
        for(var i = 0, len = data_labels.length; i < len; i++) {
            hex_color = getRandomColorHex();
            doughnut_data.push(
                {
                    value: Number(data_values[i]), //required, number, string failed
                    color: hex_color, //required
                    highlight: hex_color, //optional
                    label: data_labels[i] //optional
                }
            );
        }
        var doughnut_option = {
            responsive: true
        };

        if (typeof window.doughnut_chart != 'undefined') {
            doughnut_chart.destroy();
        }
        //$('#' + doughnut_id).get(0).getContent('2d');
        var chart_doughnut_canvas = document.getElementById(doughnut_id).getContext("2d");
        window.doughnut_chart = new Chart(chart_doughnut_canvas).Doughnut(doughnut_data, doughnut_option);
    } else {
        alert('No data.');
    }
}