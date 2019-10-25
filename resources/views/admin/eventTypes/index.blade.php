@extends('layouts.admin')
@section('content')
@can('event_type_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.event-types.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.eventType.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.eventType.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-EventType">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.eventType.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventType.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventType.fields.slug') }}
                        </th>
                        <th>
                            {{ trans('cruds.eventType.fields.photo') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventTypes as $key => $eventType)
                        <tr data-entry-id="{{ $eventType->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $eventType->id ?? '' }}
                            </td>
                            <td>
                                {{ $eventType->name ?? '' }}
                            </td>
                            <td>
                                {{ $eventType->slug ?? '' }}
                            </td>
                            <td>
                                @if($eventType->photo)
                                    <a href="{{ $eventType->photo->getUrl() }}" target="_blank">
                                        <img src="{{ $eventType->photo->getUrl('thumb') }}" width="50px" height="50px">
                                    </a>
                                @endif
                            </td>
                            <td>
                                @can('event_type_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.event-types.show', $eventType->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('event_type_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.event-types.edit', $eventType->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('event_type_delete')
                                    <form action="{{ route('admin.event-types.destroy', $eventType->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('event_type_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.event-types.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-EventType:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection