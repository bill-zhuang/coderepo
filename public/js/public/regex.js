function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function getTrimData(data) {
    return data.replace(/^\s+|\s+$/g, '');
}

function isImageExtension(img_name) {
    return (img_name.search(/\.(jpg|png|gif|jpeg|bmp)$/i) != -1);
}

function isCsvExtension(csv_name) {
    return (csv_name.search(/\.csv$/i) != -1);
}

function isUnsignedInt(digit) {
    return (digit.search(/^\s*[1-9][0-9]*\s*$/) != -1);
}

function isChineseFax(fax) {
    return (fax.search(/^\s*([0-9]{3,4}-?[0-9]{7,8}(-[0-9]{3,4})?)\s*$/) != -1);
}