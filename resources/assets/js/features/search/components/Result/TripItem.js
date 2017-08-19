import React from 'react';
import {Link} from 'react-router';
import {localize} from 'react-localize-redux';
import LangService from '../../../../app/services/LangService';
import moment from 'moment';
import '../../styles/search-trip-item.scss';

class TripItem extends React.Component {

    dateFormat(timestamp) {
        const {translate} = this.props,
            date = moment(timestamp * 1000),
            locale = moment().locale(),
            localeData = moment().locale(locale).localeData(),
            day = _.padStart(date.date(), 2, '0'),
            weekday = _.capitalize(localeData.weekdaysShort(date)),
            month = _.capitalize(localeData.monthsShort(date)),
            minute = _.padStart(date.minute(), 2, '0'),
            hour = _.padStart(date.hour(), 2, '0'),
            now = moment(),
            time = `- ${hour}:${minute}`;
        if (now.isSame(date, 'day')) {
            return `${translate('today')} ${time}`
        } else if (now.isSame(date.subtract(1, 'day'), 'day')) {
            return `${translate('tomorrow')} ${time}`
        }
        return `${weekday}. ${day} ${month} ${time}`;
    }

    render() {
        const {trip, translate} = this.props;
        return (
            <Link to={`/trip/${trip.id}`} className="search-trip-item">
                <div className="row">
                    <div className="search-trip-item__user-container col-sm-4">
                        <img src={ trip.user.photo + '?' + trip.id }
                             alt={ trip.user.full_name }
                             className="search-trip-item__user-photo"/>
                        <div className="search-trip-item__user-name"
                             title={ trip.user.full_name }
                        >{ trip.user.full_name }</div>
                        <div className="search-trip-item__user-age">{ translate('years' + LangService.getNumberForm(trip.user.age), { age: trip.user.age }) }</div>
                    </div>
                    <div className="search-trip-item__trip-container col-sm-8 clearfix">
                        <div className="search-trip-item__description">
                            <div className="search-trip-item__start-date">{ this.dateFormat(trip.start_at) }</div>
                            <div className="search-trip-item__route">
                                {trip.route.points.map((route) =>
                                    <span className={"search-trip-item__route-item" +
                                        (route.id == trip.route.from.id ? ' search-trip-item__route-item_from' : '') +
                                        (route.id == trip.route.to.id ? ' search-trip-item__route-item_to' : '')
                                    } key={route.id}>
                                        {route.point}
                                    </span>
                                )}
                            </div>
                            <div className="search-trip-item__from">
                                <i className="fa fa-circle-o search-trip-item__from-ico" title="from" /> {trip.route.from.point}
                            </div>
                            <div className="search-trip-item__to">
                                <i className="fa fa-circle-o search-trip-item__to-ico"  title="to" /> {trip.route.to.point}
                            </div>
                        </div>
                        <div className="search-trip-item__offer">
                            <div className="search-trip-item__price"><span className="search-trip-item__price-currency">$</span>{trip.price}</div>
                            <div className="search-trip-item__price-sign">{translate('per_passenger')}</div>
                            <div className="search-trip-item__free-seats">
                                <span className="search-trip-item__free-seats-text">
                                    {trip.seats}
                                </span> {translate('free_seats' + LangService.getNumberForm(trip.seats))}</div>
                        </div>
                    </div>
                </div>
            </Link>
        )
    }
}

export default localize(TripItem, 'locale');