@extends('layouts.app')
@section('title', 'Address List')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card panel-default">
                <div class="card-header">Address List</div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Postcode</th>
                            <th>Phone</th>
                            <th>Handel</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($addresses as $address)
                            <tr>
                                <td>{{ $address->contact_name }}</td>
                                <td>{{ $address->full_address }}</td>
                                <td>{{ $address->post_code }}</td>
                                <td>{{ $address->contact_phone }}</td>
                                <td>
                                    <button class="btn btn-primary">Edit</button>
                                    <button class="btn btn-danger">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection