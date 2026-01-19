<?php

namespace App\Http\Controllers\Clinics\Clinic\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Service\StoreServiceTypeRequest;
use App\Models\Clinics\Clinic\Services\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceTypeController extends Controller
{
    use AuthorizesRequests;

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

    public function store(StoreServiceTypeRequest $request)
    {
        $data = $request->validated();
        $data['clinic_id'] = Auth::user()->clinic_id;

        ServiceType::create($data);

        return redirect()->route('service-types.index')
            ->with('success', 'Tipo de serviço criado com sucesso.');
    }

    public function update(StoreServiceTypeRequest $request, ServiceType $serviceType)
    {
        $serviceType->update($request->validated());

        return redirect()->route('service-types.index')
            ->with('success', 'Serviço atualizado.');
    }

    public function destroy(ServiceType $serviceType)
    {
        $serviceType->delete();
        return redirect()->route('service-types.index')
            ->with('success', 'Serviço removido.');
    }
}
