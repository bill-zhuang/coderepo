var jCommon = (function () {
    return {
        deleteDiv: function (divID) {
            $('#' + divID).remove();
        },

        getImageSrcsInDiv: function (divID) {
            var pics = [];
            $('#' + divID + ' img').each(function () {
                pics.push($(this).attr('src'));
            });

            return pics;
        },

        batchMute: function (obj, childName) {
            $('input[name="' + childName + '"]').each(function () {
                $(this).prop('checked', obj.checked);
            });
        },

        closeBatch: function (obj, parentID) {
            if (!obj.checked) {
                $('#' + parentID).prop('checked', false);
            } else {
                if ($('input[name="' + obj.name + '"]:checked').size() == $('input[name="' + obj.name + '"]').size()) {
                    $('#' + parentID).prop('checked', true);
                }
            }
        },

        getBatchIDs: function (childName) {
            var selected = [];
            $("input[name='" + childName + "']:checked").each(function () {
                selected.push(this.value);
            });

            return selected;
        },

        getCheckboxIDs: function (divID) {
            var selected = [];
            $('#' + divID + ' input:checked').each(function () {
                selected.push(this.value);
            });

            return selected;
        },

        getFileImageCount: function (fileID) {
            return $('#' + fileID)[0]['files'].length;
        },

        getQueryStringParams: function(param) {
            var variables = window.location.search.substring(1).split('&');
            for (var i = 0; i < variables.length; i++) {
                var parameterName = variables[i].split('=');
                if (parameterName[0] == param) {
                    return parameterName[1];
                }
            }

            return '';
        }
    }
}());