$(document).ready(function() {
    initChart();
});

function initChart() {
    var get_url = '/person/finance-history/ajax-index';
    var get_data = {

    };
    var method = 'get';
    var success_function = function(result){
        //payment history data by month
        initMonthChart(result.month_chart_data);
        //all payment history data by day
        initLineChartAll(result.all_chart_data);
        //payment by category last year
        initYearCategoryChart(result.year_category_chart_data, result.year_spent);
        //payment by category last 30 days
        initMonthCategoryChart(result.month_category_chart_data, result.month_spent);
    };
    callAjaxWithFunction(get_url, get_data, success_function, method);
}

function initMonthChart(month_chart_data) {
    var data_period = month_chart_data['period'];
    var data_payment = month_chart_data['payment'];
    var line_canvas_id = 'payment_history_line_chart';
    var bar_canvas_id = 'payment_history_bar_chart';
    if (data_period.length != 0) {
        initLineChart(data_period, data_payment, line_canvas_id);
        initBarChart(data_period, data_payment, bar_canvas_id);
    }
}

function initLineChartAll(all_chart_data) {
    var data_period = all_chart_data['period'];
    var data_payment = all_chart_data['payment'];
    if (data_period.length != 0) {
        var line_data = {
        labels : data_period,
        datasets: [
            {
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: data_payment
            }
        ]
        };
        var line_option = {
            responsive: true
        };
        var chart_line_canvas = document.getElementById("payment_history_line_chart_all").getContext("2d");
        var chart_line = new Chart(chart_line_canvas).Line(line_data, line_option);
    }
}

function initYearCategoryChart(year_category_chart_data, year_spent) {
    var data_category = year_category_chart_data['category'];
    var data_payment = year_category_chart_data['payment'];
    $('#year_spent').text('(' + year_spent + ')');
    var line_canvas_id = 'category_payment_history_line_chart';
    var bar_canvas_id = 'category_payment_history_bar_chart';
    if (data_category.length != 0) {
        initLineChart(data_category, data_payment, line_canvas_id);
        initBarChart(data_category, data_payment, bar_canvas_id);
    }
}

function initMonthCategoryChart(month_category_chart_data, month_spent) {
    var data_category = month_category_chart_data['category'];
    var data_payment = month_category_chart_data['payment'];
    $('#month_spent').text('(' + month_spent + ')');
    var line_canvas_id = 'month_category_payment_history_line_chart';
    var bar_canvas_id = 'month_category_payment_history_bar_chart';
    if (data_category.length != 0) {
        initLineChart(data_category, data_payment, line_canvas_id);
        initBarChart(data_category, data_payment, bar_canvas_id);
    }
}

function initLineChart(data_x_labels, data_y_axis, line_canvas_id) {
    var line_data = {
        labels : data_x_labels,
        datasets: [
            {
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: data_y_axis
            }
        ]
    };
    var line_option = {
        responsive: true
    };
    var chart_line_canvas = document.getElementById(line_canvas_id).getContext("2d");
    var chart_line = new Chart(chart_line_canvas).Line(line_data, line_option);
}

function initBarChart(data_x_labels, data_y_axis, bar_canvas_id) {
    var bar_data = {
        labels : data_x_labels,
        datasets: [
            {
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: data_y_axis
            }
        ]
    };
    var bar_option = {
        responsive: true
    };
    var chart_bar_canvas = document.getElementById(bar_canvas_id).getContext("2d");
    var chart_bar = new Chart(chart_bar_canvas).Bar(bar_data, bar_option);
}