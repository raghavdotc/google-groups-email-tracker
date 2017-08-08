@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Email Tracker Filters</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <form action="{{ url('filters') }}">
                    <div class="form-group col-sm-2">
                        <label for="from">From Date</label>
                        <input type="date" class="form-control" value="{{old('from')}}" name="from" placeholder="From">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="from">To Date</label>
                        <input type="date" class="form-control" value="{{old('to')}}" name="to" placeholder="To">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="from">Client ID</label>
                        <select class="form-control" name="client_id" placeholder="Client ID">
                            <option value="">- Select a Client -</option>
                            @foreach($clients as $client)
                                <option value="{{$client->id}}">{{ $client->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="from">Sender ID</label>
                        <select class="form-control" name="sender_id" placeholder="Sender ID">
                            <option value="">- Select a Sender -</option>
                            @foreach($senders as $sender)
                                <option value="{{$sender->id}}">{{ $sender->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <button style="margin-top: 25px;" type="submit" class="btn btn-info">Filters</button>
                    </div>
                </form>
            </div>
            <div class="row divider">
                <br>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>
                                Sent Date
                            </th>
                            <th>
                                ClientID
                            </th>
                            <th>
                                Subject
                            </th>
                            <th>
                                Sender ID
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($emails as $email)
                            <tr>
                                <td>
                                    {{ $email->created_at }}
                                </td>
                                <td>
                                    {{ $email->client_id }}
                                </td>
                                <td>
                                    {{ $email->subject }}
                                </td>
                                <td>
                                    {{ $email->sender->email }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    {{ $emails->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection