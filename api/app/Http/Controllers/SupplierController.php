<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\SupplierIndexRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SupplierIndexRequest $request)
    {
        $query = Supplier::query();

        if ($request->boolean('trashed')) {
            $query->withTrashed();
        }

        $query->when(
            $request->id,
            fn ($q, $id) => $q->where('id', $id)
        );

        $query->when(
            $request->name,
            fn ($q, $name) => $q->where('name', 'ilike', "%{$name}%")
        );

        $query->when(
            $request->ruc,
            fn ($q, $ruc) => $q->where('ruc', 'like', "%{$ruc}%")
        );

        $query->when(
            $request->email,
            fn ($q, $email) => $q->where('email', 'ilike', "%{$email}%")
        );

        // Sorting
        $query->orderBy(
            $request->input('sort_by', 'created_at'),
            $request->input('sort_dir', 'desc')
        );

        // Pagination
        $suppliers = $query->paginate(
            $request->input('per_page', 10)
        );

        return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        return response()->json([
            'message' => 'Supplier stored succesfully',
            'code' => 'supplier.stored',
            'supplier' => new SupplierResource($supplier),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['createdBy', 'updatedBy']);

        return response()->json([
            'supplier' => new SupplierResource($supplier),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {

        $supplier->update($request->validated());

        return response()->json([
            'message' => 'Supplier updated succesfully',
            'code' => 'supplier.updated',
            'supplier' => new SupplierResource($supplier),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->trashed()) {
            return response()->json([
                'message' => 'Supplier already deleted.',
                'code' => 'supplier.alreadyDeleted',
            ], 409);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully.',
            'code' => 'supplier.deleted',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);

        if (! $supplier->trashed()) {
            return response()->json([
                'message' => 'Supplier is not deleted.',
                'code' => 'supplier.notDeleted',
            ], 409);
        }

        $supplier->restore();

        return response()->json([
            'message' => 'Supplier restored successfully.',
            'code' => 'supplier.restored',
            'supplier' => new SupplierResource($supplier),
        ]);
    }
}
