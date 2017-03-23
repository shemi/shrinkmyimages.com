const extend = function () {
    var extended = {};

    for(let key in arguments) {
        var argument = arguments[key];

        for (let prop in argument) {
            if (Object.prototype.hasOwnProperty.call(argument, prop)) {
                extended[prop] = argument[prop];
            }
        }

    }

    return extended;
};

const isEmpty = (object) => {
    if (object == null) {
        return true;
    }

    if (Array.isArray(object)) {
        return object.length === 0;
    }

    return Object.keys(object).length === 0;
};

const shallowProperty = function (key) {
    return function (obj) {
        return obj == null ? void 0 : obj[key];
    };
};

const getLength = shallowProperty('length');

const MAX_ARRAY_INDEX = Math.pow(2, 53) - 1;
const isArrayLike = function (collection) {
    var length = getLength(collection);
    return typeof length == 'number' && length >= 0 && length <= MAX_ARRAY_INDEX;
};

const flatten = function (input, shallow, strict, output) {
    output = output || [];
    let idx = output.length;

    for (let i = 0, length = getLength(input); i < length; i++) {
        var value = input[i];

        if (isArrayLike(value) && (Array.isArray(value) || value.callee)) {
            // Flatten current level of array or arguments object.
            if (shallow) {
                let j = 0, len = value.length;

                while (j < len) {
                    output[idx++] = value[j++];
                }

            } else {
                flatten(value, shallow, strict, output);

                idx = output.length;
            }
        } else if (!strict) {
            output[idx++] = value;
        }
    }

    return output;
};

export {
    extend,
    isEmpty,
    flatten
}