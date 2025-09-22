@extends('layouts.app')

@section('title', $title ?? 'Laporan')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">{{ $title ?? 'Laporan' }}</h5>
    @stack('page-actions')
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body p-3">
      @livewire($component)
    </div>
  </div>
@endsection
