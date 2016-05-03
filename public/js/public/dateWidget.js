var DateWidget = (function() {
    var jsDate = new Date();
    return {
        getCurrentTime: function() {
            return this.getYear() + '-' + this.getMonth() + '-' + this.getDay() + ' ' + this.getHour()
                + ':' + this.getMinute() + ':' + this.getSecond();
        },

        getCurrentDate: function() {
            var month = this.getMonth();
            var day = this.getDay();

            if (month < 10) {
                month = '0' + month;
            }
            if (day < 10) {
                day = '0' + day;
            }

            return this.getYear() + '-' + month + '-' + day;
        },

        getYear: function() {
            return jsDate.getFullYear();
        },

        getMonth: function() {
            return jsDate.getMonth() + 1;
        },

        getDay: function() {
            return jsDate.getDate();
        },

        getHour: function() {
            return jsDate.getHours();
        },

        getMinute: function() {
            return jsDate.getMinutes();
        },

        getSecond: function() {
            return jsDate.getSeconds();
        }
    }
}());