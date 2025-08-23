@extends('layouts.app')

@section('title','Kategori Pengeluaran')

@section('breadcrumb')
  <ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Kategori Pengeluaran</li>
  </ol>
@endsection

@section('content')
  <div class="container-fluid mb-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card shadow-sm">
          <div class="card-body">
            @include('utils.alerts')

            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Daftar Kategori</h5>
              @can('create_expense_categories')
                <a href="{{ route('expense-categories.create') }}" class="btn btn-primary">+ Tambah</a>
              @endcan
            </div>

            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle">
                <thead>
                  <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th width="140"></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($categories as $c)
                    <tr>
                      <td>{{ $c->category_name }}</td>
                      <td>{{ $c->category_description }}</td>
                      <td class="text-right">
                        @can('edit_expense_categories')
                          <a href="{{ route('expense-categories.edit', $c) }}" class="btn btn-sm btn-info">Edit</a>
                        @endcan
                        @can('delete_expense_categories')
                          <form action="{{ route('expense-categories.destroy', $c) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                          </form>
                        @endcan
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="text-center text-muted">Belum ada kategori</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            {{ $categories->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
