<?php

namespace App\Http\Requests;

use App\Venue;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreVenueRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('venue_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'name'           => [
                'required',
            ],
            'slug'           => [
                'required',
            ],
            'location_id'    => [
                'required',
                'integer',
            ],
            'event_types.*'  => [
                'integer',
            ],
            'event_types'    => [
                'array',
            ],
            'address'        => [
                'required',
            ],
            'people_minimum' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'people_maximum' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
