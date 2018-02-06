@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">Job Submissions</div>
                    <div class="panel-body">
                        <a href="{{ url('/jobs/create') }}" class="btn btn-success btn-sm" title="Add New Job">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/jobs') }}" accept-charset="UTF-8" class="navbar-form navbar-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
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
                                <div class="alert alert-success">
                                    {{ session('flash_message') }}
                                </div>
                            @endif

                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Title</th><th>Description</th><th>Email</th><th>Published</th><th>Spam</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($jobs as $item)
                                    <tr>
                                        <td>{{ $loop->iteration or $item->id }}</td>
                                        <td>{{ $item->title }}</td><td>{{ $item->description }}</td><td>{{ $item->email }}</td>
                                        @if($item->published)
                                            <td> true </td>
                                        @else
                                            <td> false </td>
                                        @endif
                                        @if($item->spam)
                                            <td> true </td>
                                        @else
                                            <td> false </td>
                                        @endif
                                        <td>
                                            <a href="{{ url('/jobs/' . $item->id) }}" title="View Job"><button class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            <a href="{{ url('/jobs/' . $item->id . '/edit') }}" title="Edit Job"><button class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                            <form method="POST" action="{{ url('/jobs' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-xs" title="Delete Job" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                            @if($user->user_type == 2 && !$item->published && !$item->spam)
                                            <form method="POST" action="{{ url('/jobs/publishOrSpam' . '/' . $item->id) }}?method=publish" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('PATCH') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-success btn-xs" title="Publish Job" onclick="return confirm(&quot;Confirm publish?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Publish</button>
                                            </form>
                                            @endif
                                            @if($user->user_type == 2 && !$item->published && !$item->spam)
                                                <form method="POST" action="{{ url('/jobs/publishOrSpam' . '/' . $item->id) }}?method=spam" accept-charset="UTF-8" style="display:inline">
                                                    {{ method_field('PATCH') }}
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-danger btn-xs" title="Mar as Spam" onclick="return confirm(&quot;Confirm spam?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Spam</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $jobs->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
