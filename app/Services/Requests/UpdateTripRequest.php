<?php

namespace App\Services\Requests;

use Carbon\Carbon;

interface UpdateTripRequest
{
    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return int
     */
    public function getSeats(): int;

    /**
     * @return Carbon
     */
    public function getStartAt(): Carbon;

    /**
     * @return Carbon
     */
    public function getEndAt(): Carbon;

    /**
     * @return int
     */
    public function getVehicleId(): int;

    /**
     * @return array
     */
    public function getFrom(): array;

    /**
     * @return array
     */
    public function getTo(): array;

    /**
     * @return array
     */
    public function getWaypoints(): array;

    /**
     * @return array
     */
    public function getRoutesTime(): array;

    /**
     * @return int
     */
    public function getLuggageSize(): int;

    /**
     * @return bool
     */
    public function getIsAnimalsAllowed(): bool;
}
