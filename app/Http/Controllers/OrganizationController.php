<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Organization as OrganizationResource;

class OrganizationController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return OrganizationResource::collection(DB::table('organizations')->paginate(10));
    }

    public function show(int $id): OrganizationResource
    {
        return new OrganizationResource(Organization::find($id));
    }
}
