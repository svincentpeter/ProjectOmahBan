@extends('layouts.app')

@section('title','Tambah Kategori')

@section('breadcrumb')
  <ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">Kategori Pengeluaran</a></li>
    <li class="breadcrumb-item active">Tambah</li>
  </ol>
@endsection

@section('content')
  <div class="container-fluid mb-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card shadow-sm">
          <div class="card-body">
            @include('utils.alerts')

            <form action="{{ route('expense-categories.store') }}" method="POST">
              @include('expense::categories._form', ['category' => null])
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
