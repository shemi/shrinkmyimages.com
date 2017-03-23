import {extend} from './helpers';

/**
 * Initialize the SMI form extension points.
 */
window.SMI.forms = {
    register: {},
    login: {}
};


import './form';
import './errors';

import http from './http';

Object.assign(window.SMI, http);
