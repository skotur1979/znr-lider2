<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// MODELI koje štitimo "po vlasniku"
use App\Models\Employee;
use App\Models\Machine;
use App\Models\Fire;
use App\Models\Miscellaneous;

// JEDAN generički policy za sve vlasničke modele
use App\Policies\OwnedPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class      => OwnedPolicy::class,
        Machine::class       => OwnedPolicy::class,
        Fire::class          => OwnedPolicy::class,
        Category::class      => OwnedPolicy::class,
        Miscellaneous::class => OwnedPolicy::class,
        Incident::class      => OwnedPolicy::class,
        Chemical::class      => OwnedPolicy::class,
        FirstAidKit::class   => OwnedPolicy::class,
        Observation::class   => OwnedPolicy::class,
        PersonalProtectiveEquipmentLog::class => OwnedPolicy::class,
        DocumentationItem::class => OwnedPolicy::class,
        RiskAssessment::class => OwnedPolicy::class,
        RiskAttachment::class => OwnedPolicy::class,
        MedicalRefferal::class => OwnedPolicy::class,
        

        // Ako kasnije želiš dodati još “po korisniku” modele:
        // \App\Models\Chemical::class         => OwnedPolicy::class,
        // \App\Models\Observation::class      => OwnedPolicy::class,
        // \App\Models\Incident::class         => OwnedPolicy::class,
        // \App\Models\FirstAidKit::class      => OwnedPolicy::class,
        // \App\Models\PersonalProtectiveEquipmentLog::class => OwnedPolicy::class,
        // ...
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // Nema dodatnih Gate pravila – sav access je kroz policy.
    }
}

