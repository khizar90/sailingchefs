@extends('layouts.master')

@section('dashboard')
<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title mb-5 text-center">List of users</h1>
              <table class="table mt-5">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">email</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $user)

                  <tr>
                    <th>{{$user->id}}</th>
                    <td>{{$user->full_name}}</td>
                    
                    <td>
                        {{$user->email}}                      
                    </td>
                    <td>
                        {{$user->type}}                      
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              
        </div>
    </div>
</div>
@endsection