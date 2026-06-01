const API_BASE =
    window.location.origin +
    '/api';

const Permissions = {

    dashboard:
        ['admin','manager','sales','viewer'],

    offers:
        ['admin','manager','sales','viewer'],

    users:
        ['admin'],

    settings:
        ['admin'],

    audit:
        ['admin','manager'],

    status:
        ['admin']
};