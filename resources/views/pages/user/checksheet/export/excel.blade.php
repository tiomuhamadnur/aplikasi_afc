<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $equipment->name }}</title>
</head>

<body>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;"> # </th>
                <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">Date</th>
                @foreach ($parameters as $parameter)
                    <th style="border: 3px; background-color:gray; font-weight:bolder; text-align:center;">
                        {{ $parameter->name }}
                        @if ($parameter->tipe == 'number')
                            ({{ $parameter->satuan->code ?? '-' }})
                            <br>
                            ({{ $parameter->min_value }} - {{ $parameter->max_value }})
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($pivotData as $date => $values)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $date }}</td>
                    @foreach ($parameters as $param)
                        @php
                            $value = $values->get($param->name, '-');
                            $isOutOfRange =
                                is_numeric($value) && ($value < $param->min_value || $value > $param->max_value);
                        @endphp
                        <td
                            @if ($isOutOfRange) title="Out of Tolerance" class="fw-bolder" style="background-color: #ff6969" @endif>
                            {{ $value }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
