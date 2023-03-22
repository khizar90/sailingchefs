@extends('layouts.master')

@section('dashboard')
<div class="col-md-9">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title mb-5 text-center">Catgory</h1>
            <form class="form-inline" action="{{route('catgory')}}" method="post">
                @csrf
                <h5>Add catgory</h5>
                <div class="form-group mx-sm-3 mb-2">
                  <label for="catgory" class="sr-only">Catgory</label>
                  <input type="text" class="form-control" id="Catgory" name="catgory" placeholder="Catgory">          
                </div>
                <button type="submit" class="btn btn-primary mb-2">add catgory</button>
              </form>
              <span class="text-danger">
                @error('catgory')
                {{$message}}
                @enderror
                </span>
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
              <h3 class="text-center mt-5">List of catgory</h3>

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
                  @foreach ($catgories as $catgory)

                  <tr>
                    <th>{{$catgory->id}}</th>
                    <td>{{$catgory->name}}</td>
                    <td>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCatgoryModal{{$catgory->id}}">
                        Edit
                      </button>
                      <!-- Edit Catgory Modal -->
                      <div class="modal fade" id="editCatgoryModal{{$catgory->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editCatgoryModal{{$catgory->id}}Label" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="editCatgoryModal{{$catgory->id}}Label">Edit Catgory</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <form class="form-inline" action="{{route('updateCatgory', $catgory->id)}}" method="post">
                                          @csrf
                                          <div class="form-group mx-sm-3 mb-2">
                                              <label for="catgory" class="sr-only">Catgory</label>
                                              <input type="text" class="form-control" id="catgory" name="catgory" value="{{$catgory->name}}">          
                                          </div>
                                          <button type="submit" class="btn btn-primary mb-2">Edit Catgory</button>
                                      </form>
                                      
                                  </div>
                              </div>
                          </div>
                      </div>
    
                  

                    </td>
                      

                    </td>
                    <td>
                      <a href="{{route('deleteCatgory', $catgory->id)}}"><button type="button" class="btn btn-danger">Delete</button></a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              
        </div>
    </div>
</div>
@endsection