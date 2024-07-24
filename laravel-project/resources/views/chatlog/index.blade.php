<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
        </script>
    </head>
    <body>
        <div class="container mt-5">
            <p>Hi, {{$sysname}}!</p>
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
        <div class="container mt-5">
            <form method="POST" action="/home/add">
                @csrf
                <div class="form-group mb-2">
                    <label for="logtitle">Title</label>
                    <input type="text" class="form-control" name="title" placeholder="New Chat">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
            <table class="table mt-5">
                <thead>
                    <tr>
                        <th scope="col">LogId</th>
                        <th scope="col">Title</th>
                        <th scope="col">created_at</th>
                        <th scope="col">updated_at</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($logs) > 0)
                        @foreach ($logs as $log)
                            <tr>
                                <th>{{ $log->logid }}</th>
                                <th>{{ $log->title }}</th>
                                <th>{{ $log->created_at }}</th>
                                <th>{{ $log->updated_at }}</th>
                                <th>
                                    <a href="/chat" class="btn btn-primary">Chat</a>
                                    <a href="/home/edit/{{ $log->logid }}" class="btn btn-primary">Edit</a>
                                    <a href="/home/delete/{{ $log->logid }}" class="btn btn-danger">Delete</a>
                                </th>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th>No Data</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </body>
</html>
