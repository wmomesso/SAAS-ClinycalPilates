<?php

namespace App\Http\Controllers\Clinics\Clinic\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Finance\StoreServicePackageRequest;
use App\Models\Clinics\Clinic\Finance\ServicePackage;
use App\Models\Clinics\Clinic\Services\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServicePackageController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // Assume-se a existência de uma ServicePackagePolicy registrada
        $this->authorizeResource(ServicePackage::class, 'service_package');
    }

    /**
     * Lista os pacotes de serviços da clínica.
     */
    public function index()
    {
        $packages = ServicePackage::where('clinic_id', Auth::user()->clinic_id)
            ->with('serviceType')
            ->orderBy('name')
            ->get();

        return view('clinic.finance.packages.index', compact('packages'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        $serviceTypes = ServiceType::where('clinic_id', Auth::user()->clinic_id)
            ->where('is_active', true)
            ->get();

        return view('clinic.finance.packages.create', compact('serviceTypes'));
    }

    /**
     * Armazena um novo pacote de serviço.
     */
    public function store(StoreServicePackageRequest $request)
    {
        $data = $request->validated();
        $data['clinic_id'] = Auth::user()->clinic_id;

        ServicePackage::create($data);

        return redirect()->route('service-packages.index')
            ->with('success', 'Pacote de serviço criado com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(ServicePackage $servicePackage)
    {
        $serviceTypes = ServiceType::where('clinic_id', Auth::user()->clinic_id)
            ->where('is_active', true)
            ->get();

        return view('clinic.finance.packages.edit', compact('servicePackage', 'serviceTypes'));
    }

    /**
     * Atualiza o pacote de serviço.
     */
    public function update(StoreServicePackageRequest $request, ServicePackage $servicePackage)
    {
        $servicePackage->update($request->validated());

        return redirect()->route('service-packages.index')
            ->with('success', 'Pacote de serviço atualizado com sucesso.');
    }

    /**
     * Remove o pacote de serviço.
     */
    public function destroy(ServicePackage $servicePackage)
    {
        $servicePackage->delete();

        return redirect()->route('service-packages.index')
            ->with('success', 'Pacote de serviço removido.');
    }
}
