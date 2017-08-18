<?php

namespace App\Services;

use App\Criteria\Trips\AllDriverTripsCriteria;
use App\Criteria\Trips\DriverTripByIdCriteria;
use App\Criteria\Trips\PastDriverTripsCriteria;
use App\Criteria\Trips\UpcomingDriverTripsCriteria;
use App\Models\Trip;
use App\Repositories\TripRepository;
use App\Rules\DeleteTrip\TripOwnerRule;
use App\Exceptions\Trip\TripNotFoundException;
use App\Exceptions\Trip\UserCantEditTripException;
use App\Validators\UpdateTripValidator;
use Illuminate\Support\Facades\Validator;
use App\Services\Requests\CreateTripRequest;
use App\Services\Requests\UpdateTripRequest;
use App\User;
use App\Validators\DeleteTripValidator;
use App\Validators\RestoreTripValidator;
use Prettus\Repository\Contracts\CriteriaInterface;

class TripsService
{
    private $tripRepository;
    private $deleteTripValidator;
    private $restoreTripValidator;
    private $updateTripValidator;

    /**
     * TripsService constructor.
     *
     * @param TripRepository $tripRepository
     * @param DeleteTripValidator $deleteTripValidator
     * @param RestoreTripValidator $restoreTripValidator
     * @param UpdateTripValidator $updateTripValidator
     */
    public function __construct(
        TripRepository $tripRepository,
        DeleteTripValidator $deleteTripValidator,
        RestoreTripValidator $restoreTripValidator,
        UpdateTripValidator $updateTripValidator
    )
    {
        $this->tripRepository = $tripRepository;
        $this->deleteTripValidator = $deleteTripValidator;
        $this->restoreTripValidator = $restoreTripValidator;
        $this->updateTripValidator = $updateTripValidator;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getAll(User $user)
    {
        return $this->tripRepository->getByCriteria(new AllDriverTripsCriteria($user));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getUpcoming(User $user)
    {
        return $this->tripRepository->getByCriteria(new UpcomingDriverTripsCriteria($user));
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getPast(User $user)
    {
        return $this->tripRepository->getByCriteria(new PastDriverTripsCriteria($user));
    }

    /**
     * @param CreateTripRequest $request
     * @param $user
     * @return Trip
     */
    public function create(CreateTripRequest $request, $user): Trip
    {
        $tripAttributes = [
            'price' => $request->getPrice(),
            'seats' => $request->getSeats(),
            'start_at' => $request->getStartAt(),
            'end_at' => $request->getEndAt(),
            'vehicle_id' => $request->getVehicleId(),
            'user_id' => $user->id,
        ];

        $routeAttributes = [
            'from' => $request->getFrom(),
            'to' => $request->getTo(),
        ];

        $trip = $this->tripRepository->save(new Trip($tripAttributes));
        $trip->routes()->create($routeAttributes);

        return $trip;
    }

    /**
     * Get user trip by id
     *
     * @param Trip $trip
     * @param User $user
     * @return mixed
     */
    public function show(Trip $trip, User $user)
    {
        return $this->tripRepository->getByCriteria(new DriverTripByIdCriteria($trip, $user));
    }

    /**
     * Update trip service
     *
     * @param Trip $trip
     * @param UpdateTripRequest $request
     * @param $user
     * @return mixed
     */
    public function update(Trip $trip, UpdateTripRequest $request, $user)
    {
        $this->updateTripValidator->validate($trip, $user);

        $tripAttributes = [
            'price' => $request->getPrice(),
            'seats' => $request->getSeats(),
            'start_at' => $request->getStartAt(),
            'end_at' => $request->getEndAt(),
            'vehicle_id' => $request->getVehicleId(),
        ];

        $routeAttributes = [
            'from' => $request->getFrom(),
            'to' => $request->getTo(),
        ];

        $result = $this->tripRepository->update($tripAttributes, $trip->id); // don't use this way of storing models. Your repository shouldn't know about arrays

        // PROBLEM :: $result->routes() return null
        $result->routes()->update($routeAttributes);
        // Error response 500 - array to string conversion

        return $result;
    }

    /**
     * @param Trip $trip
     * @param $user
     * @return Trip
     */
    public function delete(Trip $trip, $user)
    {
        $this->deleteTripValidator->validate($trip, $user);
        $this->tripRepository->softDelete($trip);

        return $trip;
    }

    /**
     * @param Trip $trip
     * @param $user
     * @return Trip
     */
    public function restore(Trip $trip, $user)
    {
        $this->restoreTripValidator->validate($trip, $user);
        $this->tripRepository->restore($trip);

        return $trip;
    }
}
