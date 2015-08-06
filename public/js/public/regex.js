function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function getTrimData(data) {
    return data.replace(/^\s+|\s+$/g, '');
}

function isImageExtension(img_name) {
    if (img_name.search(/\.(jpg|png|gif|jpeg|bmp)$/i) != -1) {
        return true;
    } else {
        return false;
    }
}

function isCsvExtension(csv_name) {
    if (csv_name.search(/\.csv$/i) != -1) {
        return true;
    } else {
        return false;
    }
}

function isUnsignedInt(digit) {
    if (digit.search(/^\s*[1-9][0-9]*\s*$/) != -1) {
        return true;
    } else {
        return false;
    }
}

function isChineseFax(fax) {
    if (fax.search(/^\s*([0-9]{3,4}-?[0-9]{7,8}(-[0-9]{3,4})?)\s*$/) != -1) {
        return true;
    } else {
        return false;
    }
}