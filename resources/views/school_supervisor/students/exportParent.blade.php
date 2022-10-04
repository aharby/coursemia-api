<table class="table">
    <thead>
    <tr>
        <th class="text-center">{{ trans('students.student name') }}</th>
        <th class="text-center">{{ trans('parents.parent name') }}</th>
        <th class="text-center">{{ trans('parents.username') }}</th>
        <th class="text-center">{{ trans('parents.password') }}</th>
        <th class="text-center">{{ trans('parents.created on') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        @foreach($row->user->parents as $parent)
            <tr class="text-center">
                <td>{{ $row->user->first_name.' '.$row->user->last_name }}</td>
                <td>{{ $parent->first_name.' '.$parent->last_name }}</td>
                <td>{{ $parent->username }}</td>
                <td>{{ $parent->parentData->password ?? '' }}</td>
                <td>{{ $row->created_at }}</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
