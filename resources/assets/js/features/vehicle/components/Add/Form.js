import React from 'react';
import { connect } from 'react-redux';
import { doCreate } from '../../actions';
import Input from './Input';
import Select from 'react-select';
import { VehicleValidate } from '../../../../app/services/VehicleService';
import { securedRequest } from '../../../../app/services/RequestService';

class Form extends React.Component {

    constructor(props) {
        super(props);
        this.onSubmit = this.onSubmit.bind(this);
        this.onSave = this.onSave.bind(this);
        this.logChange = this.logChange.bind(this);
        this.state = {
            errors: {},
            brand: {
                selected: false,
                placeholder: 'Select vehicle brand',
            },
            model: {
                selected: false,
                disabled: true,
                placeholder: 'Select vehicle brand first',
            },
            year: false,
            color: {
                selected: false,
                placeholder: 'Select vehicle color',
            },
            body: {
                selected: false,
                placeholder: 'Select vehicle body',
            },
            seats: false,
            photo: false,
        };


        this.getBrandOptions = this.getBrandOptions.bind(this);
        this.getModelOptions = this.getModelOptions.bind(this);
        this.getBodyOptions = this.getBodyOptions.bind(this);
        this.getColorOptions = this.getColorOptions.bind(this);
    }

    onSubmit(event) {
        event.preventDefault();
        const vehicleData = {
                brand: event.target['brand'].value,
                model: event.target['model'].value,
                year: event.target['year'].value,
                color: event.target['color'].value,
                body: event.target['body'].value,
                seats: event.target['seats'].value,
                photo: event.target['photo'].value,
            };

        const val = VehicleValidate(vehicleData);
        console.log(val);

        if (!val.valid) {
            this.setState({
                errors: val.errors
            });
        } else {
            this.props.dispatch(doCreate(vehicleData));
        }

    };

    onSave(event) {
        this.props.actions.doLogin(this.state.credentials);
    }

    componentWillReceiveProps(nextProps) {
        if (nextProps.successCreate) {
            browserHistory.push('/mycars');
        }
    }

    logChange(val) {

        console.log("Selected: " + JSON.stringify(val));
    };

    getBrandOptions = () => {
        return securedRequest.get(`/api/v1/car-brand`)
            .then((response) => {
                return response.data;
            }).then((json) => {
                return { options: json };
            });
    }

    getModelOptions = () => {
        // let idBrand = this.state.brand.selected;
        return securedRequest.get(`/api/v1/car-brand/1/models`)
            .then((response) => {
                return response.data;
            }).then((json) => {
                json.forEach(function(element) { delete element.id_car_mark });
                return { options: json };
            });
    }

    // getModelOptions = (idBrand) => {
    //     if (idBrand) {
    //         return securedRequest.get(`/api/v1/carModel/${idBrand}`)
    //             .then((response) => {
    //                 return response.data;
    //             }).then((json) => {
    //                 json.forEach(function(element) { delete element.id_car_mark });
    //                 return { options: json };
    //             });
    //     } else {
    //         return Promise.resolve({ options: [] });
    //     }
    // }

    getBodyOptions = () => {
      return securedRequest.get(`/api/v1/car-body`)
        .then((response) => {
          return response.data;
        }).then((json) => {
          return { options: json };
        });
    }

    getColorOptions = () => {
      return securedRequest.get(`/api/v1/car-color`)
        .then((response) => {
          return response.data;
        }).then((json) => {
          return { options: json };
        });
    }

    render() {
        const { errors } = this.state;

        return (
            <form role="form" className="card vehicle-form" action="/api/v1/car" method="POST"
                  onSubmit={ this.onSubmit }>
                <div className="card-header">
                    Enter vehicle details
                </div>
                <div className="card-block">
                    <div className="form-group row ">
                        <label className="form-control-label text-muted col-sm-4" htmlFor="brand">Vehicle Brand</label>
                        <Select.Async
                            name="brand"
                            placeholder={ this.state.brand.placeholder }
                            value={ this.state.brand.selected ? this.state.brand.selected : this.state.brand.placeholder }
                            // valueKey="id_car_mark"
                            valueKey="name"
                            labelKey="name"
                            className="col-sm-8"
                            clearable={ false }
                            loadOptions={ this.getBrandOptions }
                            onChange={ data => {
                                this.setState({
                                        brand: {
                                            selected: data ? data.id_car_mark : 1
                                        },
                                        model: {
                                            disabled: false
                                        }
                                    });
                                }
                            }
                        />
                    </div>

                    <div className="form-group row ">
                        <label className="form-control-label text-muted col-sm-4" htmlFor="model">Vehicle Model</label>
                        <Select.Async
                            name="model"
                            placeholder={ this.state.model.placeholder }
                            value={ this.state.model.selected ? this.state.model.selected : this.state.model.placeholder }
                            // valueKey="id_car_model"
                            valueKey="name"
                            labelKey="name"
                            className="col-sm-8"
                            clearable={ false }
                            disabled={ this.state.model.disabled }
                            // loadOptions={ this.getModelOptions(this.state.brand.selected ? this.state.brand.selected : 1) }
                            loadOptions={ this.getModelOptions }
                            onChange={ data => {
                                this.setState({
                                    model: {
                                        selected: data ? data.id_car_model : 1
                                    },
                                })
                            } }
                        />
                    </div>

                    <Input
                        type="year"
                        name="year"
                        id="year"
                        error={ errors.year }
                        onChange={ data => {
                            this.setState({
                                    year: data.year
                                });
                            }
                        }

                    >Year</Input>

                    <div className="form-group row ">
                        <label className="form-control-label text-muted col-sm-4" htmlFor="color">Color</label>
                        <Select.Async
                            name="color"
                            placeholder={ this.state.color.placeholder }
                            value={ this.state.color.selected ? this.state.color.selected : this.state.color.placeholder }
                            // valueKey="id"
                            valueKey="color"
                            labelKey="color"
                            className="col-sm-8"
                            clearable={ false }
                            loadOptions={ this.getColorOptions }
                            onChange={ data => {
                                this.setState({
                                    color: {
                                        selected: data ? data.id : 1
                                    },
                                })
                            } }
                        />
                    </div>

                    <div className="form-group row ">
                        <label className="form-control-label text-muted col-sm-4" htmlFor="body">Body</label>
                        <Select.Async
                            name="body"
                            placeholder={ this.state.body.placeholder }
                            value={ this.state.body.selected ? this.state.body.selected : this.state.body.placeholder }
                            // valueKey="id"
                            valueKey="body"
                            labelKey="body"
                            className="col-sm-8"
                            clearable={ false }
                            loadOptions={ this.getBodyOptions }
                            onChange={ data => {
                                this.setState({
                                    body: {
                                        selected: data ? data.id : 1
                                    },
                                })
                            } }
                        />
                    </div>

                    <Input
                        type="text"
                        name="seats"
                        id="seats"
                        onChange={ data => {
                            this.setState({
                                    seats: data.seats
                                });
                            }
                        }
                        error={ errors.seats }
                    >Seats</Input>
                    <Input
                        type="text"
                        name="photo"
                        id="photo"
                        onChange={ data => {
                            this.setState({
                                    photo: data.photo
                                });
                            }
                        }
                        error={ errors.photo }
                    >Photo</Input>
                </div>

                <div className="card-footer">
                    <div className="text-center">
                        <button className="btn btn-primary">
                            Create
                        </button>
                    </div>
                </div>
            </form>
        );
    }
}

const FormConnected = connect(
    (state) => ({
        errors: state.vehicle.create.errors,
        successCreate: state.vehicle.create.success
    })
)(Form);

export default FormConnected;