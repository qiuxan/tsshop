@extends('layouts.app')
@section('title', 'Address List')

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card panel-default">
                <div class="card-header">
                    Address List
                    <a href="{{ route('user_addresses.create') }}" class="float-right">Add Shipping Address
                    </a>
                </div>
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
                                    <a href="{{ route('user_addresses.edit', $address->id) }}" class="btn btn-primary">Edit</a>

                                    <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">Delete</button>

                                    {{--<form action="{{route('user_addresses.destroy',$address->id)}}" method="POST" style="display: inline-block">--}}
                                        {{--@method('delete')--}}
                                        {{--{{csrf_field()}}--}}
                                        {{--<button class="btn btn-danger">Delete</button>--}}
                                    {{--</form>--}}
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

@section('scriptsAfterJs')
    <script>
        $(document).ready(function() {
            $('.btn-del-address').click(function() {
                var id = $(this).data('id');
                swal({
                    title: "Are you sure you want to delete this address？",
                    icon: "warning",
                    buttons: ['No', 'Yes'],
                    dangerMode: true,
                })
                    .then(function(willDelete) {
                        if (!willDelete) {
                            return;
                        }
                        axios.delete('/user_addresses/' + id)
                            .then(function () {
                                location.reload();
                            })
                    });
            });
        });
    </script>
@endsection