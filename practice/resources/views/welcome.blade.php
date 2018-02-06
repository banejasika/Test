<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: top;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            display: inline-block;
            vertical-align: top;
            background-color: white;
        }

        .title {
            color: black;
            font-weight: bold;
            background-color: #fff;
            border-color: #d3e0e9;
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
            font-family: "Raleway", sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .table-borderless, .search {
            color: black;
            font-weight: bold;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/jobs') }}">Jobs</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    @endif

    <div class="content">
        @extends('layouts.welcome')

        @section('content')
            <div class="title">Job Submissions</div>
            <div class="panel-body">

                <form method="GET" action="{{ url('/search') }}" accept-charset="UTF-8" class="navbar-form navbar-right"
                      role="search">
                    <div class="input-group">
                        <input type="text" class="search" name="search" placeholder="Search..."
                               value="{{ request('search') }}">
                        <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                    </div>
                </form>
                <br/>
                <br/>
                <div class="table-responsive">
                    @if (session('flash_message'))
                        <div class="alert alert-success" style="font-weight: bold">
                            {{ session('flash_message') }}
                        </div>
                    @endif
                        @if (session('flash_alert'))
                            <div class="alert alert-danger" style="font-weight: bold">
                                {{ session('flash_alert') }}
                            </div>
                        @endif
                    <table class="table table-borderless">
                        <thead>
                        <tr>
                            <th>#</th><th>Title</th><th>Description</th><th>Email</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($jobs as $item)
                            <tr>
                                <td>{{ $loop->iteration or $item->id }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->email }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-wrapper"> {!! $jobs->appends(['search' => Request::get('search')])->render() !!} </div>
                </div>

            </div>
    </div>
</div>
@endsection
</body>
</html>
