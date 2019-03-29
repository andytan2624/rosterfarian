@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h2>Your Roster</h2>
            <div class="table-responsive">

            @foreach ($roster as $weekIndex => $week)
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($location_list as $location)
                            <tr>
                                <td>{{ $location['location_name'] }}</td>
                                <td>{{ implode($roster[$weekIndex]['mon'][$location['id']]['nominations'], ', ') }}</td>
                                <td>{{ implode($roster[$weekIndex]['tue'][$location['id']]['nominations'], ', ') }}</td>
                                <td>{{ implode($roster[$weekIndex]['wed'][$location['id']]['nominations'], ', ') }}</td>
                                <td>{{ implode($roster[$weekIndex]['thur'][$location['id']]['nominations'], ', ') }}</td>
                                <td>{{ implode($roster[$weekIndex]['fri'][$location['id']]['nominations'], ', ') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>Day Off</td>
                            <td>{{ $day_off_roster[$weekIndex]['mon'] }}</td>
                            <td>{{ $day_off_roster[$weekIndex]['tue'] }}</td>
                            <td>{{ $day_off_roster[$weekIndex]['wed'] }}</td>
                            <td>{{ $day_off_roster[$weekIndex]['thur'] }}</td>
                            <td>{{ $day_off_roster[$weekIndex]['fri'] }}</td>
                        </tr>
                    </tbody>
                </table>

            @endforeach
            </div>
        </div>
    </div>
@endsection
