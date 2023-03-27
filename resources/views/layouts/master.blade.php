<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item"><a href="{{route('catgory')}}">Catagory</a></li>
                    <li class="list-group-item"><a href="{{route('dietary')}}">Dietary</a></li>
                    <li class="list-group-item"><a href="{{route('allergies')}}">Allergies</a></li>
                    <li class="list-group-item"><a href="{{route('userList')}}">Users</a></li>

                </ul>
            </div>
            @yield('dashboard')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>

    <script>
        var myModal = document.getElementById('myModal')
        var myInput = document.getElementById('myInput')
        myModal.addEventListener('shown.bs.modal', function () {
            myInput.focus()
        })
    </script>


<script>
//   $('#name').on('keyup', function() {
//   const searchQuery = $(this).val();

//   $.ajax({
//     url: '{{ route('search') }}',
//     method: 'GET',
//     data: {
//       name: searchQuery
//     },
//     success: function(data) {
     
//       const tableBody = $('#search-results tbody');
//       tableBody.empty(); 

//       data.forEach(function(user) {
//         const row = `<tr>
//           <td>${user.id}</td>
//           <td>${user.full_name}</td>
//           <td>${user.email}</td>
//           <td>${user.type}</td>
//         </tr>`;
//         tableBody.append(row);
//       });
//     }
//   });
// });
  $(document).ready(function() {
    $('#search-form').submit(function(event) {
      event.preventDefault();
      var searchQuery = $('#name').val();
      $.ajax({
        url: "{{ route('search') }}",
        method: 'get',
        data: {
          name: searchQuery
        },
        success: function(data) {

          const tableBody = $('#search-results tbody');
          tableBody.empty();
          
          if (data.length > 0) {
            data.forEach(function(user) {
              const row = `<tr>
              <td>${user.id}</td>
              <td>${user.full_name}</td>
              <td>${user.email}</td>
              <td>${user.type}</td>
              </tr>`;
              tableBody.append(row);
            });
          }else {
            tableBody.append(`<tr class='text-center'><td colspan="4"><h1>No search results found.<h1></td></tr>`);
          }
        },
        error: function(xhr) {
          console.log(xhr.responseText);
        }
      });
    });
  });
</script>

</body>
</html>    


