<?php

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Events\Repository\EventRepositoryInterface;
use App\Modules\Events\Resources\Admin\ListAdminEventsIndex;
use Illuminate\Http\Request;

class EventsAdminApiControllers extends Controller
{
    public function __construct(
        public EventRepositoryInterface $eventRepository
    )
    {
    }

    public function index()
    {
        $events = $this->eventRepository->all();
        return response()->json([
            'total' => $events->total(),
            'events' => ListAdminEventsIndex::collection($events->items())
        ]);
    }

    public function show($id)
    {
        $event = $this->eventRepository->find($id);
        if ($event) {
            return customResponse(new ListAdminEventsIndex($event), '', 200, 1);
        };
        return customResponse('', trans('api.no country found'), 404, 2);
    }

    public function store(Request $request)
    {
        $data = [];
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($request->has('title_en')) {
            $data['title:en'] = $request->get('title_en');
        }
        if ($request->has('title_ar')) {
            $data['title:ar'] = $request->get('title_ar');
        }
        if ($request->has('event_url')) {
            $data['event_url'] = $request->get('event_url');
        }
        if ($request->has('image')) {
            $data['image'] = moveSingleGarbageMedia($request->get('image'), 'events');
        }
        if ($this->eventRepository->create($data)) {
            return customResponse('', trans('api.Created Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

    public function update(Request $request, $id)
    {
        $data = [];
        if ($request->has('is_active')) {
            $data['is_active'] = $request->get('is_active');
        }
        if ($request->has('title_en')) {
            $data['title:en'] = $request->get('title_en');
        }
        if ($request->has('title_ar')) {
            $data['title:ar'] = $request->get('title_ar');
        }
        if ($request->has('event_url')) {
            $data['event_url'] = $request->get('event_url');
        }
        if (isset($request->image)) {
            $data['image'] = moveSingleGarbageMedia($request->get('image'), 'events');
        }
        if ($this->eventRepository->update($id, $data)) {
            return customResponse('', trans('api.Updated Successfully'), 200, 1);
        }
        return customResponse('', trans('api.oops something went wrong'), 400, 2);

    }

    public function destroy($id)
    {
        if ($this->eventRepository->delete($id)) {
            return customResponse('', trans('api.Deleted Successfully'), 200, 1);
        };
        return customResponse('', trans('api.oops something went wrong'), 400, 2);
    }

}
