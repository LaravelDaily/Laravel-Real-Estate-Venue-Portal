<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreVenueRequest;
use App\Http\Requests\UpdateVenueRequest;
use App\Http\Resources\Admin\VenueResource;
use App\Venue;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VenuesApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('venue_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VenueResource(Venue::with(['location', 'event_types'])->get());
    }

    public function store(StoreVenueRequest $request)
    {
        $venue = Venue::create($request->all());
        $venue->event_types()->sync($request->input('event_types', []));

        if ($request->input('main_photo', false)) {
            $venue->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
        }

        if ($request->input('gallery', false)) {
            $venue->addMedia(storage_path('tmp/uploads/' . $request->input('gallery')))->toMediaCollection('gallery');
        }

        return (new VenueResource($venue))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Venue $venue)
    {
        abort_if(Gate::denies('venue_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new VenueResource($venue->load(['location', 'event_types']));
    }

    public function update(UpdateVenueRequest $request, Venue $venue)
    {
        $venue->update($request->all());
        $venue->event_types()->sync($request->input('event_types', []));

        if ($request->input('main_photo', false)) {
            if (!$venue->main_photo || $request->input('main_photo') !== $venue->main_photo->file_name) {
                $venue->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
            }
        } elseif ($venue->main_photo) {
            $venue->main_photo->delete();
        }

        if ($request->input('gallery', false)) {
            if (!$venue->gallery || $request->input('gallery') !== $venue->gallery->file_name) {
                $venue->addMedia(storage_path('tmp/uploads/' . $request->input('gallery')))->toMediaCollection('gallery');
            }
        } elseif ($venue->gallery) {
            $venue->gallery->delete();
        }

        return (new VenueResource($venue))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Venue $venue)
    {
        abort_if(Gate::denies('venue_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $venue->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
