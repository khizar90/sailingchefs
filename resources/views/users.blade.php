@extends('layouts.master')

@section('dashboard')
<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title mb-5 text-center">List of users</h1>
            <form class="row g-3"   id="search-form" >
              <div class="col-auto">
                <input type="text" class="form-control" id="name" name="name" placeholder="Search">
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Search</button>
              </div>
            </form>
            <span class="text-danger">
              @error('name')
              {{$message}}
              @enderror
            </span>
            @if(count($users) > 0)
            <table class="table mt-5" id="search-results">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <th>{{$user->id}}</th>
                            <td>{{$user->full_name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->type}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No search results found.</p>
        @endif
              
        </div>
    </div>
</div>

@endsection