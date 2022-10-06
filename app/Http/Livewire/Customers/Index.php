<?php

namespace App\Http\Livewire\Customers;

use Livewire\Component;
use App\Http\Livewire\WithConfirmation;
use App\Http\Livewire\WithSorting;
use Illuminate\Support\Facades\Gate;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;

class Index extends Component
{
    use WithPagination, WithSorting, WithConfirmation, WithFileUploads;

    public int $perPage;

    public $listeners = ['confirmDelete', 'delete', 'export', 'import'];

    public array $orderable;

    public $selectPage;

    public string $search = '';

    public array $selected = [];

    public array $paginationOptions;

    protected $queryString = [
        'search' => [
            'except' => '',
        ],
        'sortBy' => [
            'except' => 'id',
        ],
        'sortDirection' => [
            'except' => 'desc',
        ],
    ];

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetSelected()
    {
        $this->selected = [];
    }

    public function mount()
    {
        $this->selectPage = false;
        $this->sortBy            = 'id';
        $this->sortDirection     = 'desc';
        $this->perPage           = 100;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable = (new Customer())->orderable;
    }

    public function render()
    {
        abort_if(Gate::denies('customer_access'), 403);

        $query = Customer::advancedFilter([
            's'               => $this->search ?: null,
            'order_column'    => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $customers = $query->paginate($this->perPage);

        return view('livewire.customers.index', compact('customers'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('customer_delete'), 403);

        Customer::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(Customer $customer)
    {
        abort_if(Gate::denies('customer_delete'), 403);

        $customer->delete();
    }

    public function showModal(Customer $customer)
    {
        abort_if(Gate::denies('customer_show'), 403);

        $this->emit('showModal', $customer);
    }

    public function editModal(Customer $customer)
    {
        abort_if(Gate::denies('customer_edit'), 403);

        $this->emit('editModal', $customer);
    }

    public function restore(Customer $customer)
    {
        abort_if(Gate::denies('customer_delete'), 403);

        $customer->restore();
    }

    public function forceDelete(Customer $customer)
    {
        abort_if(Gate::denies('customer_delete'), 403);

        $customer->forceDelete();
    }

    public function downloadSelected()
    {
        abort_if(Gate::denies('customer_access'), 403);

        $customers = Customer::whereIn('id', $this->selected)->get();

        return (new CustomerExport($customers))->download('customers.xlsx');
    }

    public function downloadAll()
    {
        abort_if(Gate::denies('customer_access'), 403);

        $customers = Customer::all();

        return (new CustomerExport($customers))->download('customers.xlsx');
    }

    public function exportSelected()
    {
        abort_if(Gate::denies('customer_access'), 403);

        $customers = Customer::whereIn('id', $this->selected)->get();

        return (new CustomerExport($customers))->download('customers.pdf');
    }

    public function exportAll()
    {
        abort_if(Gate::denies('customer_access'), 403);

        $customers = Customer::all();

        return (new CustomerExport($customers))->download('customers.pdf');
    }

    public function import()
    {
        abort_if(Gate::denies('customer_access'), 403);

        $this->validate([
            'import_file' => [
                'required',
                'file',
            ],
        ]);

        Customer::import(new CustomerImport, request()->file('import_file'));

        $this->reset('import_file');
    }

}
