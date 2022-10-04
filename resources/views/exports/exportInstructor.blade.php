<table>
    <thead>
    <tr>
        <th>Instructor Id</th>
        <th>Instructor name</th>
        <th>Subjects</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            @php
                $subjects = $row->schoolInstructorSubjects()->get();
            @endphp
            <td>{{ $row->id }}</td>
            <td>{{ $row->name }}</td>
            <td>
                <table>
                    @foreach($subjects as $subject)
                        <tr>
                            <td>subId {{$subject->id }} subName: {{$subject->name }} G: {{ $subject->gradeClass->title }}</td>
                        </tr>
                    @endforeach
                    <tr></tr>
                </table>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
