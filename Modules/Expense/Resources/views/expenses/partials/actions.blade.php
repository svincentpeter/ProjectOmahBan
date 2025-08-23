@can('edit_expenses')
    <a href="{{ route('expenses.edit', $e) }}" class="btn btn-sm btn-info">
        <i class="bi bi-pencil"></i> Edit
    </a>
@endcan

@can('delete_expenses')
    <form action="{{ route('expenses.destroy', $e) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Hapus data ini?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-danger">
            <i class="bi bi-trash"></i> Hapus
        </button>
    </form>
@endcan
