@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">Job {{ $job->id }}</div>
                    <div class="panel-body">

                        <a href="{{ url('/jobs') }}" title="Back"><button class="btn btn-warning btn-xs"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/jobs/' . $job->id . '/edit') }}" title="Edit Job"><button class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                        <form method="POST" action="{{ url('jobs' . '/' . $job->id) }}" accept-charset="UTF-8" style="display:inline">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger btn-xs" title="Delete Job" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                        </form>
                        @if($user->user_type == 2 && !$job->published && !$job->spam)
                            <form method="POST" action="{{ url('/jobs/publishOrSpam' . '/' . $job->id) }}?method=publish" accept-charset="UTF-8" style="display:inline">
                                {{ method_field('PATCH') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-success btn-xs" title="Publish Job" onclick="return confirm(&quot;Confirm publish?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Publish</button>
                            </form>
                        @endif
                        @if($user->user_type == 2 && !$job->published && !$job->spam)
                            <form method="POST" action="{{ url('/jobs/publishOrSpam' . '/' . $job->id) }}?method=spam" accept-charset="UTF-8" style="display:inline">
                                {{ method_field('PATCH') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-danger btn-xs" title="Publish Job" onclick="return confirm(&quot;Confirm spam?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Spam</button>
                            </form>
                        @endif
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th>ID</th><td>{{ $job->id }}</td>
                                    </tr>
                                    <tr><th> Title </th><td> {{ $job->title }} </td></tr><tr><th> Description </th><td> {{ $job->description }} </td></tr><tr><th> Email </th><td> {{ $job->email }} </td>
                                        <tr>
                                        <th>Published</th>
                                        @if($job->published)
                                            <td> true </td>
                                        @else
                                            <td> false </td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Spam</th>
                                        @if($job->spam)
                                            <td> true </td>
                                        @else
                                            <td> false </td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
