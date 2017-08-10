import * as actions from './actionTypes';
import axios from 'axios';

const tripsData = [
    {id: 1,from:['49.82380909','24.03808194'], to:['49.55372551','25.59814053'], brand: 'Car1', model: '1', start_at:'2017-08-10 12:21:31', end_at:'2017-08-10 14:21:31'},
    {id: 2,from:['49.82380909','24.03808194'], to:['49.55372551','30.59814053'], brand: 'Car2', model: '1', start_at:'2017-08-10 12:21:31', end_at:'2017-08-10 14:21:31'},
    {id: 3,from:['49.82380909','24.03808194'], to:['50.55372551','27.59814053'], brand: 'Car3', model: '2', start_at:'2017-08-10 08:21:31', end_at:'2017-08-10 09:21:31'},
    {id: 5,from:['49.82380909','24.03808194'], to:['60.55372551','25.59814053'], brand: 'Car3', model: '2', start_at:'2017-09-10 08:21:31', end_at:'2017-08-10 09:21:31'},
    {id: 4,from:['49.82380909','24.03808194'], to:['64.55372551','25.59814053'], brand: 'Car4', model: '2', start_at:'2017-08-10 08:21:31', end_at:'2017-08-10 09:21:31'}
];

const user_id = 1;

export function getTrips() {
    // axios.get('api/driver?user_id='+user_id)
    //     .then(function (response) {
    //         console.log(response);
    //     })
    //     .catch(function (error) {
    //         console.log(error);
    //     });
    return {
        type: actions.TRIPSLIST_GET_ALL,
        trips: tripsData
    };
};

export function filterPast() {
    return {
        type: actions.TRIPSLIST_FILTER_PAST,
        trips: tripsData
    };
};

export function filterUpcoming() {
    return {
        type: actions.TRIPSLIST_FILTER_UPCOMING,
        trips: tripsData
    };
};