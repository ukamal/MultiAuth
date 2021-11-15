@extends('user.master')
@section('user')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="container">
    <div class="row">
        <div class="col-md-12 m-5">
            <form action="{{ route('store-profile') }}" method="post" enctype="multipart/form-data">
                @csrf 

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" aria-describedby="name" value="{{ $editData->name }}">
                  </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Email address</label>
                  <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" value="{{ $editData->email }}">
                </div>

                <div class="mb-3">
                  <label for="image" class="form-label">Image</label>
                  <input type="file" class="form-control" id="image" name="profile_photo_path">
                </div>
                <div class="mb-3">
                    <img src="{{ (!empty($editData->profile_photo_path))? url('upload/userimages/'.$editData->profile_photo_path):url('upload/no-img.png') }}" 
                        alt="img" id="showImg">
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
              </form>
        </div>
    </div>
</div>  
    

<script type="text/javascript">
    $(document).ready(function (){
        $('#image').change(function (e){
            var reader = new FileReader();
            reader.onload = function (e){
                $('#showImg').attr('src',e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>


@endsection