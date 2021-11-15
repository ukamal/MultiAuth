@extends('user.master')
@section('user')


<div class="container m-5">
  <div class="row">
    <div class="col-md-12">
      <div class="card" style="width: 18rem;">
  
        <img src="{{ (!empty($user->profile_photo_path))? url('upload/userimages/'.$user->profile_photo_path):url('upload/no-img.png') }}" alt="img">
        
          <div class="card-body">
            <h5 class="card-title">{{ $user->name }}</h5>
            <p class="card-text">{{ $user->email }}</p>
            <a href="{{ route('edit-profile') }}" class="btn btn-primary">Edit Profile</a>
          </div>
          
        </div>
    </div>
  </div>
</div>



@endsection