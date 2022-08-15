<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist</title>
</head>

<body>
    <ol>
        @foreach ($checklists as $key => $result)
        <li>{{$key}} :: <strong>{{$result}}</strong></li>
        @endforeach
    </ol>
</body>

</html>