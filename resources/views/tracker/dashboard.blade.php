@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Emails Dashboard</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form action="{{ url('dashboard') }}">
                        <div class="form-group col-sm-2">
                            <label for="from">From Date</label>
                            <input type="date" class="form-control" value="{{old('from')}}" name="from"
                                   placeholder="From">
                        </div>
                        <div class="form-group col-sm-2">
                            <label for="from">To Date</label>
                            <input type="date" class="form-control" value="{{old('to')}}" name="to" placeholder="To">
                        </div>
                        <div class="form-group col-sm-2">
                            <button style="margin-top: 25px;" type="submit" class="btn btn-info">Get Counts</button>
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
                                    ClientID
                                </th>
                                <th>
                                    Count
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($counts as $count)
                                <tr>
                                    <td>
                                        {{ $count->client_id }}
                                    </td>
                                    <td>
                                        {{ $count->count }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        {{ $counts->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection