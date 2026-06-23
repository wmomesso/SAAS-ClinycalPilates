<?php

namespace App\Http\Controllers\Clinics\Clinic\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Service\StoreServiceTypeRequest;
use App\Models\Clinics\Clinic\Services\ServiceType;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ServiceTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ServiceType::class, 'service_type');
    }

    public function index()
    {
        $serviceTypes = ServiceType::where('clinic_id', Auth::user()->clinic_id)
            ->orderBy('name')
            ->get();

        return view('clinic.services.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('clinic.services.create');
    }

    public function store(StoreServiceTypeRequest $request)
    {
        $data = $request->validated();
        $data['clinic_id'] = Auth::user()->clinic_id;

        ServiceType::create($data);

        Alert::success('Sucesso', 'Tipo de serviço criado com sucesso.');

        return redirect()->route('service-types.index');
    }

    public function edit(ServiceType $serviceType)
    {
        return view('clinic.services.edit', compact('serviceType'));
    }

    public function update(StoreServiceTypeRequest $request, ServiceType $serviceType)
    {
        $serviceType->update($request->validated());

        Alert::success('Sucesso', 'Serviço atualizado.');

        return redirect()->route('service-types.index');
    }

    public function destroy(ServiceType $serviceType)
    {
        $serviceType->delete();

        Alert::success('Sucesso', 'Serviço removido.');

        return redirect()->route('service-types.index');
    }
}
