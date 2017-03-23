import Vue from 'vue';
import {isEmpty, flatten} from './helpers';

/**
 * Spark form error collection class.
 */
window.SmiFormErrors = function () {
    this.errors = {};

    /**
     * Determine if the collection has any errors.
     */
    this.hasErrors = function () {
        return ! isEmpty(this.errors);
    };


    /**
     * Determine if the collection has errors for a given field.
     */
    this.has = function (field) {
        return Object.keys(this.errors).indexOf(field) > -1;
    };


    /**
     * Get all of the raw errors for the collection.
     */
    this.all = function () {
        return this.errors;
    };


    /**
     * Get all of the errors for the collection in a flat array.
     */
    this.flatten = function () {
        let errors = this.errors;

        if (!errors || isEmpty(errors)) {
            return [];
        }

        errors = Object.values(errors);

        return flatten(errors);
    };


    /**
     * Get the first error message for a given field.
     */
    this.get = function (field) {
        if (this.has(field)) {
            if(Array.isArray(this.errors[field])) {
                return this.errors[field][0];
            } else if(typeof this.errors[field] == 'string') {
                return this.errors[field];
            }
        }
    };


    /**
     * Set the raw errors for the collection.
     */
    this.set = function (errors) {
        if (typeof errors === 'object') {
            this.errors = errors;
        } else {
            this.errors = {'form': ['Something went wrong. Please try again or contact customer support.']};
        }
    };


    /**
     * Remove errors from the collection.
     */
    this.forget = function (field) {
        if (typeof field === 'undefined') {
            this.errors = {};
        } else {
            Vue.delete(this.errors, field);
        }
    };
};
