<?php

namespace Modules\People\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\People\DataTables\CustomersDataTable;
use Modules\People\Entities\Customer;
use Modules\People\Http\Requests\StoreCustomerRequest;
use Modules\People\Http\Requests\UpdateCustomerRequest;

class CustomersController extends Controller
{
    public function index(CustomersDataTable $dataTable)
    {
        abort_if(Gate::denies('access_customers'), 403);
        return $dataTable->render('people::customers.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_customers'), 403);
        $cities = Customer::getUniqueCities();
        return view('people::customers.create', compact('cities'));
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $validated = $request->validated();
            Customer::create($validated);

            toast('Customer berhasil ditambahkan!', 'success');
            return redirect()->route('customers.index');
        } catch (\Throwable $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
            toast('Gagal menambahkan customer. Silakan coba lagi.', 'error');
            return back()->withInput();
        }
    }

    public function show(Customer $customer)
    {
        abort_if(Gate::denies('show_customers'), 403);

        $customer->load([
            'sales' => fn($q) => $q->latest()->take(10),
        ]);

        $stats = [
            'total_sales' => $customer->sales()->count(),
            'total_amount' => $customer->sales()->sum('total_amount'),
            'total_paid' => $customer->sales()->sum('paid_amount'),
            'total_due' => $customer->sales()->sum('due_amount'),
            'last_sale_date' => $customer->sales()->latest('date')->value('date'),
        ];

        return view('people::customers.show', compact('customer', 'stats'));
    }

    public function edit(Customer $customer)
    {
        abort_if(Gate::denies('edit_customers'), 403);

        $cities = Customer::getUniqueCities();
        $hasSales = $customer->sales()->exists();

        return view('people::customers.edit', compact('customer', 'cities', 'hasSales'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        try {
            $customer->update($request->validated());

            toast('Data customer berhasil diperbarui!', 'success');
            return redirect()->route('customers.index');
        } catch (\Throwable $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            toast('Gagal memperbarui customer. Silakan coba lagi.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(Customer $customer)
    {
        abort_if(Gate::denies('delete_customers'), 403);

        try {
            $hasSales = $customer->sales()->exists();

            if ($hasSales) {
                $customer->delete(); // soft delete
                toast('Customer berhasil diarsipkan.', 'warning');
            } else {
                $customer->forceDelete(); // hard delete
                toast('Customer berhasil dihapus permanen.', 'success');
            }

            return redirect()->route('customers.index');
        } catch (\Throwable $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());
            toast('Gagal menghapus customer. Silakan coba lagi.', 'error');
            return back();
        }
    }

    /**
     * Endpoint Select2 (AJAX).
     * Menerima: q | term | search, page, per_page, selected_id, ids[]
     * Return:
     * { results:[{id,text,name,email,phone,city,address}], pagination:{more:bool} }
     */
    public function getCustomers(Request $request)
    {
        // Kompatibel dengan berbagai lib: q / term / search
        $search = trim((string) $request->get('q', $request->get('term', $request->get('search', ''))));
        $page = (int) max(1, (int) $request->get('page', 1));
        $perPage = (int) max(1, (int) $request->get('per_page', 10));

        // Preselect single
        if ($request->filled('selected_id')) {
            $c = Customer::find($request->integer('selected_id'));
            if (!$c) {
                return response()->json(['results' => [], 'pagination' => ['more' => false]]);
            }
            return response()->json([
                'results' => [
                    [
                        'id' => $c->id,
                        'text' => $this->composeLabel($c),
                        'name' => $c->customer_name,
                        'email' => (string) ($c->customer_email ?? ''),
                        'phone' => (string) ($c->customer_phone ?? ''),
                        'city' => (string) ($c->city ?? ''),
                        'address' => (string) ($c->address ?? ''),
                    ],
                ],
                'pagination' => ['more' => false],
            ]);
        }

        // Preselect multiple
        if ($request->filled('ids') && is_array($request->get('ids'))) {
            $items = Customer::whereIn('id', $request->get('ids', []))->get();
            $results = $items
                ->map(
                    fn($c) => [
                        'id' => $c->id,
                        'customer_name' => $c->customer_name,
                        'customer_email' => (string) ($c->customer_email ?? ''),
                        'customer_phone' => (string) ($c->customer_phone ?? ''),
                        'city' => (string) ($c->city ?? ''),
                        'text' => $c->customer_name, // wajib untuk Select2
                    ],
                )
                ->values();

            return response()->json(['results' => $results, 'pagination' => ['more' => false]]);
        }

        $builder = Customer::query();

        // Pencarian
        if ($search !== '') {
            $builder->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // 🔧 FIX PENTING: jangan terlalu ketat di is_active
        if (Schema::hasColumn('customers', 'is_active')) {
            $builder->where(function ($q) {
                $q->whereNull('is_active')->orWhere('is_active', 1);
            });
        }

        $total = $builder->count();

        $items = $builder
            ->orderBy('customer_name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['id', 'customer_name', 'customer_email', 'customer_phone', 'city', 'address']);

        $results = $items
            ->map(
                fn($c) => [
                    'id' => $c->id,
                    'text' => $this->composeLabel($c),
                    'name' => $c->customer_name,
                    'email' => (string) ($c->customer_email ?? ''),
                    'phone' => (string) ($c->customer_phone ?? ''),
                    'city' => (string) ($c->city ?? ''),
                    'address' => (string) ($c->address ?? ''),
                ],
            )
            ->values();

        $hasMore = $page * $perPage < $total;

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $hasMore],
        ]);
    }

    private function composeLabel(Customer $c): string
    {
        // Label yang muncul di dropdown Select2
        return trim($c->customer_name . ' — ' . ($c->customer_phone ?: '-') . ' — ' . ($c->customer_email ?: '-') . ' — ' . ($c->city ?: '-'));
    }

    public function storeOrGet(Request $request)
    {
        $normalized = $request->all();
        if (!isset($normalized['name']) && isset($normalized['customer_name'])) {
            $normalized['name'] = $normalized['customer_name'];
        }

        $data = validator($normalized, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ])->validate();

        $existing = null;
        if (!empty($data['email'])) {
            $existing = Customer::where('customer_email', $data['email'])->first();
        }
        if (!$existing && !empty($data['phone'])) {
            $existing = Customer::where('customer_phone', $data['phone'])->first();
        }

        if ($existing) {
            return response()->json([
                'success' => true,
                'id' => $existing->id,
                'name' => $existing->customer_name,
                'email' => (string) ($existing->customer_email ?? ''),
                'phone' => (string) ($existing->customer_phone ?? ''),
                'city' => (string) ($existing->city ?? ''),
                'address' => (string) ($existing->address ?? ''),
                'customer' => $existing,
                'message' => 'Customer ditemukan.',
            ]);
        }

        $c = Customer::create([
            'customer_name' => $data['name'],
            'customer_email' => $data['email'] ?? null,
            'customer_phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => 'Indonesia',
            'address' => $data['address'] ?? null,
        ]);

        return response()->json(
            [
                'success' => true,
                'id' => $c->id,
                'name' => $c->customer_name,
                'email' => (string) ($c->customer_email ?? ''),
                'phone' => (string) ($c->customer_phone ?? ''),
                'city' => (string) ($c->city ?? ''),
                'address' => (string) ($c->address ?? ''),
                'customer' => $c,
                'message' => 'Customer berhasil ditambahkan.',
            ],
            201,
        );
    }

    public function archived()
    {
        abort_if(Gate::denies('access_customers'), 403);

        $customers = Customer::onlyTrashed()->withCount('sales')->latest('deleted_at')->paginate(20);

        return view('people::customers.archived', compact('customers'));
    }

    public function restore($id)
    {
        abort_if(Gate::denies('edit_customers'), 403);

        try {
            $customer = Customer::onlyTrashed()->findOrFail($id);
            $customer->restore();

            toast('Customer berhasil dikembalikan!', 'success');
            return redirect()->route('customers.index');
        } catch (\Throwable $e) {
            Log::error('Error restoring customer: ' . $e->getMessage());
            toast('Gagal mengembalikan customer.', 'error');
            return back();
        }
    }

    public function statistics()
    {
        abort_if(Gate::denies('access_customers'), 403);

        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::activeInPeriod(6)->count(),
            'total_cities' => Customer::distinct('city')->count('city'),
            'total_sales' => \Modules\Sale\Entities\Sale::count(),
        ];

        return response()->json($stats);
    }
}
