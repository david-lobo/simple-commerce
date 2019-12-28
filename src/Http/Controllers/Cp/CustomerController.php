<?php

namespace Damcclean\Commerce\Http\Controllers\Cp;

use Damcclean\Commerce\Facades\Customer;
use Damcclean\Commerce\Http\Requests\CustomerStoreRequest;
use Damcclean\Commerce\Http\Requests\CustomerUpdateRequest;
use Illuminate\Http\Request;
use Statamic\CP\Breadcrumbs;
use Statamic\Facades\Blueprint;
use Statamic\Http\Controllers\CP\CpController;

class CustomerController extends CpController
{
    public function index()
    {
        $crumbs = Breadcrumbs::make([
            ['text' => 'Commerce', 'url' => '/commerce'],
        ]);

        $customers = Customer::all()
            ->map(function ($customer) {
                return array_merge($customer->toArray(), [
                    'edit_url' => cp_route('customers.edit', ['customer' => $customer['id']]),
                    'delete_url' => cp_route('customers.destroy', ['customer' => $customer['id']]),
                ]);
            });

        return view('commerce::cp.customers.index', [
            'customers' => $customers,
            'crumbs' => $crumbs,
        ]);
    }

    public function create()
    {
        $crumbs = Breadcrumbs::make([
            ['text' => 'Commerce', 'url' => '/commerce'],
            ['text' => 'Customers', 'url' => '/customers'],
        ]);

        $blueprint = Blueprint::find('customer');

        $fields = $blueprint->fields();
        $fields = $fields->addValues([]);
        $fields = $fields->preProcess();

        return view('commerce::cp.customers.create', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
            'crumbs'    => $crumbs,
        ]);
    }

    public function store(CustomerStoreRequest $request)
    {
        $validated = $request->validated();

        $customer = Customer::save($request->all());

        return ['redirect' => cp_route('customers.edit', ['customer' => $customer->data['id']])];
    }

    public function edit($customer)
    {
        $crumbs = Breadcrumbs::make([
            ['text' => 'Commerce', 'url' => '/commerce'],
            ['text' => 'Customers', 'url' => '/customers'],
        ]);

        $customer = Customer::find($customer);

        $blueprint = Blueprint::find('customer');

        $fields = $blueprint->fields();
        $fields = $fields->addValues([]);
        $fields = $fields->preProcess();

        return view('commerce::cp.customers.edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $customer,
            'meta'      => $fields->meta(),
            'crumbs'    => $crumbs,
        ]);
    }

    public function update(CustomerUpdateRequest $request, $customer)
    {
        $validated = $request->validated();

        return Customer::update(Customer::find($customer)->toArray()['id'], $request->all());
    }

    public function destroy($customer)
    {
        $customer = Customer::delete(Customer::find($customer)['slug']);

        return redirect(cp_route('customers.index'));
    }
}
