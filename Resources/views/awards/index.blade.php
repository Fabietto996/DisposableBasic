@extends('app')
@section('title', __('DBasic::common.awards'))

@section('content')
  <div class="row row-cols-4">
    @foreach($awards as $award)
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              {{ $award->name }}
              <i class="fas fa-trophy float-end"></i>
            </h5>
          </div>
          <div class="card-body p-1 text-center">
            @if($award->image_url)
              <img class="img-mh150" src="{{ $award->image_url }}" alt="{{ $award->name }}" title="{{ $award->description }}">
            @endif
          </div>
          {{--}}
          <div class="card-footer p-1 text-center small">
            {{ $award->description }}
          </div>
          {{--}}
        </div>
      </div>
    @endforeach
  </div>
@endsection
