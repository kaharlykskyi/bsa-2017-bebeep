<?php

namespace Tests\Feature\Trips;

use App\User;
use App\Models\Trip;
use App\Models\Vehicle;

class DeleteTripTest extends BaseTripTestCase
{
    protected $url = 'api/trip/delete';
    protected $method = 'DELETE';

    /**
     * @test
     */
    public function user_can_not_delete_trip_if_trip_id_is_not_correct()
    {
        $user = $this->getDriverUser();
        $vehicle = factory(Vehicle::class)->create(['user_id' => $user->id]);
        $trip = factory(Trip::class)->create(['user_id' => $user->id, 'vehicle_id' => $vehicle->id]);

        $this->url = $this->getUrl($trip->id + 1);

        $response = $this->jsonAsUser($user);
        $response->assertStatus(404);

        $this->assertDatabaseHas('trips', ['id' => $trip->id]);
    }

    /**
     * @test
     */
    public function user_cant_delete_not_his_trip()
    {
        $user = $this->getDriverUser();
        $user2 = $this->getDriverUser();
        $vehicle = factory(Vehicle::class)->create(['user_id' => $user2->id]);
        $trip = factory(Trip::class)->create(['user_id' => $user2->id, 'vehicle_id' => $vehicle->id]);

        $this->url = $this->getUrl($trip->id);

        $response = $this->jsonAsUser($user);
        $response->assertStatus(422);

        $this->assertDatabaseHas('trips', ['id' => $trip->id]);
    }

    /**
     * @test
     */
    public function user_can_delete_trip()
    {
        $user = $this->getDriverUser();
        $vehicle = factory(Vehicle::class)->create(['user_id' => $user->id]);
        $trip = factory(Trip::class)->create(['user_id' => $user->id, 'vehicle_id' => $vehicle->id]);

        $this->url = $this->getUrl($trip->id);

        $response = $this->jsonAsUser($user);
        $response->assertStatus(200);

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'deleted_at' => $response->json()['deleted_at'],
        ]);
    }

    /**
     * @test
     */
    public function user_can_not_delete_trip_without_driver_permissions()
    {
        $user = factory(User::class)->create();
        $vehicle = factory(Vehicle::class)->create(['user_id' => $user->id]);
        $trip = factory(Trip::class)->create(['user_id' => $user->id, 'vehicle_id' => $vehicle->id]);

        $this->url = $this->getUrl($trip->id);

        $response = $this->jsonAsUser($user);
        $response->assertStatus(403);

        $this->assertDatabaseHas('trips', ['id' => $trip->id]);
    }

    /**
     * Get url from trips.delete route.
     *
     * @param $id
     * @return string
     */
    private function getUrl($id)
    {
        return route('trips.delete', $id);
    }
}
