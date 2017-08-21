<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\Requests\BookingStatusRequest as BookingStatusRequestContract;
use Illuminate\Support\Str;

class BookingStatusRequest extends FormRequest implements BookingStatusRequestContract
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required'
        ];
    }

    public function getStatus(): string
    {
        return (string) Str::lower($this->get('status'));
    }
}
