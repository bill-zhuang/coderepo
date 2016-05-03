var RegexWidget = (function(){
    return {
        trim: function(str) {
            return str.replace(/^\s+|\s+$/g, '');
        },

        isNumber: function(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        },

        isUnsignedInt: function(digit) {
            if (digit === 0 || digit === '0') {
                return true;
            }
            return (digit.search(/^\s*[1-9][0-9]*\s*$/) != -1);
        },

        isPositiveInt: function(num) {
            return /^\s*[1-9][0-9]*\s*$/.test(num);
        },

        isUnsignedFloat: function(num) {
            if (num === 0 || num === '0') {
                return true;
            }

            return /^\s*[0-9]+(\.[0-9]+)?\s*$/.test(num);
        },

        isPositiveFloat: function(num) {
            return /^\s*[0-9]+(\.[0-9]+)?\s*$/.test(num);
        },

        isFloat: function(num) {
            return /^\s*-?[0-9]+(\.[0-9]+)?\s*$/.test(num);
        },

        isImageExtension: function(imageName) {
            return (imageName.search(/\.(jpg|png|gif|jpeg|bmp)$/i) != -1);
        },

        isCellPhone: function(phone) {
            return /^\s*1[0-9]{10}\s*$/.test(phone);
        },

        isChineseFax: function(fax) {
            return (fax.search(/^\s*([0-9]{3,4}-?[0-9]{7,8}(-[0-9]{3,4})?)\s*$/) != -1);
        }
    }
}());