<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Log</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">NO</th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">TANGGAL</th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">WAKTU</th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">BANK</th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">PAN</th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">ELAPSED TIME</th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">TRANSACTION SPEED
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time_stamp)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time_stamp)->format('H:i:s') }}</td>
                    <td>{{ $item->bank }}</td>
                    <td>{{ $item->pan }}</td>
                    <td>{{ $item->elapsed_time }}</td>
                    <td>{{ $item->transaction_speed }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
