<?php

namespace Tests\Unit;

use App\Models\Route;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Services\SearchTripsWithTransfersService;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchTripWithTransfersServiceTest extends TestCase
{
    const POINT_A = [50, 52];
    const POINT_B = [54, 56];
    const POINT_C = [58, 60];
    const POINT_D = [60, 62];

    /**
     * @test
     */
    public function case_1()
    {
        $this->createTrip([self::POINT_A, self::POINT_B]);
        $this->createTrip([self::POINT_B, self::POINT_C]);
        $this->createTrip([self::POINT_B, self::POINT_A, self::POINT_C]);

        $service = app()->make(SearchTripsWithTransfersService::class);
        $searchRequest = new SearchTripRequest();
        $searchRequest->fromLat = self::POINT_A[0];
        $searchRequest->fromLng = self::POINT_A[1];
        $searchRequest->toLat = self::POINT_C[0];
        $searchRequest->toLng = self::POINT_C[1];

        $possibleRoutes = $service->search($searchRequest);

        $this->assertEquals(2, $possibleRoutes->count());
    }

    /**
     * @test
     */
    public function case_2()
    {
        $this->createTrips([self::POINT_A, self::POINT_B], 2);
        $this->createTrips([self::POINT_B, self::POINT_C], 2);

        $service = app()->make(SearchTripsWithTransfersService::class);
        $searchRequest = new SearchTripRequest();
        $searchRequest->fromLat = self::POINT_A[0];
        $searchRequest->fromLng = self::POINT_A[1];
        $searchRequest->toLat = self::POINT_C[0];
        $searchRequest->toLng = self::POINT_C[1];

        $possibleRoutes = $service->search($searchRequest);

        $this->assertEquals(4, $possibleRoutes->count());
    }

    /**
     * @test
     */
    public function case_3()
    {
        $this->createTrip([self::POINT_A, self::POINT_D]);
        $this->createTrip([self::POINT_A, self::POINT_B]);
        $this->createTrip([self::POINT_B, self::POINT_C]);
        $this->createTrip([self::POINT_C, self::POINT_D]);

        $service = app()->make(SearchTripsWithTransfersService::class);
        $searchRequest = new SearchTripRequest();
        $searchRequest->fromLat = self::POINT_A[0];
        $searchRequest->fromLng = self::POINT_A[1];
        $searchRequest->toLat = self::POINT_D[0];
        $searchRequest->toLng = self::POINT_D[1];

        $searchRequest->transfers = 3;
        $possibleRoutes = $service->search($searchRequest);
        $this->assertEquals(2, $possibleRoutes->count());

        $searchRequest->transfers = 1;
        $possibleRoutes = $service->search($searchRequest);
        $this->assertEquals(1, $possibleRoutes->count());
    }

    /**
     * @param $routes
     * @param $count
     */
    private function createTrips($routes, $count) {
        foreach (range(1, $count) as $item) {
            $this->createTrip($routes);
        }
    }

    /**
     * @param $routes
     * @return mixed
     */
    private function createTrip($routes)
    {
        $driver = factory(User::class)->create([
            'permissions' => User::DRIVER_PERMISSION,
        ]);

        $vehicle = factory(Vehicle::class)->create([
            'user_id' => $driver->id,
        ]);

        $trip = factory(Trip::class)->create([
            'user_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
        ]);

        foreach ($this->getRoutesFromWaypoints($routes) as $route) {
            $trip->routes()->create($route);
        }

        return $trip;
    }

    /**
     * @param $waypoints
     * @return \Illuminate\Support\Collection
     */
    private function getRoutesFromWaypoints($waypoints)
    {
        $tripWaypoints = collect([]);
        $routes = collect([]);

        if (! empty($waypoints)) {
            foreach ($waypoints as $tripWaypoint) {
                $tripWaypoints->push($tripWaypoint);
            }
        }

        foreach (range(0, $tripWaypoints->count() - 2) as $iteration) {
            $chunk = $tripWaypoints->slice($iteration, 2)->values();

            $routes->push([
                'from' => implode('|', $chunk[0]),
                'from_lat' => $chunk[0][0],
                'from_lng' => $chunk[0][1],
                'to' => implode('|', $chunk[1]),
                'to_lat' => $chunk[1][0],
                'to_lng' => $chunk[1][1],
            ]);
        }

        return $routes;
    }
}
