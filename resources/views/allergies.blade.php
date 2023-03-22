@extends('layouts.master')

@section('dashboard')
<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title mb-5 text-center">Allergies</h1>
            <form class="form-inline" method="post" action="{{route('addAllergies')}}">
              @csrf
                <h5>Add allergies</h5>
                <div class="form-group mx-sm-3 mb-2">
                  <label for="allergies" class="sr-only">Allergies</label>
                  <input type="text" class="form-control" id="allergies" name="allergies" placeholder="allergies">
                </div>
                <button type="submit" class="btn btn-primary mb-2">add allergies</button>
              </form>
              @if (session('delete'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{ session('delete') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>

              
              @endif
              @if(session()->has('add'))
              <div class="alert alert-success">
                {{ session()->get('add') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
              <span class="text-danger">
                @error('allergies')
                {{$message}}
                @enderror
              </span>
              <h3 class="text-center mt-5">List of allergies</h3>
              <table class="table mt-5">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delet</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($allergies as $allergy)

                  <tr>
                    <th>{{$allergy->id}}</th>
                    <td>{{$allergy->name}}</td>
                    <td>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCatgoryModal{{$allergy->id}}">
                        Edit
                      </button>
                      <!-- Edit Catgory Modal -->
                      <div class="modal fade" id="editCatgoryModal{{$allergy->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editCatgoryModal{{$allergy->id}}Label" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="editCatgoryModal{{$allergy->id}}Label">Edit Allergy</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <form class="form-inline" action="{{route('updateAllergy', $allergy->id)}}" method="post">
                                          @csrf
                                          <div class="form-group mx-sm-3 mb-2">
                                              <label for="allergy" class="sr-only">Allergy</label>
                                              <input type="text" class="form-control" id="allergy" name="allergy" value="{{$allergy->name}}">          
                                          </div>
                                          <button type="submit" class="btn btn-primary mb-2">Edit Allergy</button>
                                      </form>
                                      
                                  </div>
                              </div>
                          </div>
                      </div>
                    </td>
                    <td>
                      <a href="{{route('deleteAllergies', $allergy->id)}}"><button type="button" class="btn btn-danger">Delete</button></a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              
        </div>
    </div>
</div>
@endsection