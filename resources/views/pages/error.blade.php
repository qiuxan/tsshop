@extends('layouts.app')
@section('title', 'Error')

@section('content')
    <div class="card">
        <div class="card-header">错误</div>
        <div class="card-body text-center">
            <h1>{{ $msg }}</h1>
            <a class="btn btn-primary" href="{{ route('root') }}">Back To Home Page</a>
        </div>
    </div>
@endsection