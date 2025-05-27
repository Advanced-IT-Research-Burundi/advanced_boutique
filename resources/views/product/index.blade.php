@extends('layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="section-title">Produits</h5>
                        <div>
                            <button type="button" class="btn btn-primary" id="save-settings">
                                <i class="bi bi-save"></i> Enregistrer nouveaux produits
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Produits -->
                            <div class="tab-pane fade" id="products" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Les produits</h5>
                                    <a type="button" class="btn btn-sm btn-success" href="{{ route('products.create') }}">
                                        <i class="bi bi-plus"></i> Ajouter un produit
                                    </a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover border">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nom</th>
                                                <th>Description</th>
                                                <th>Catégorie</th>
                                                <th>PU</th>
                                                <th>PV</th>
                                                <th>Unité</th>
                                                <th>Quantité seuil</th>
                                                <th>Agence</th>
                                                {{-- <th>Actions</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                                style="width: 32px; height: 32px;">
                                                                <i class="bi bi-box"></i>
                                                            </div>
                                                            {{ $product->name }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $product->description }}</td>
                                                    <td>{{ $product->category_id }}</td>
                                                    <td>
                                                        {{ $product->purchase_price }}
                                                    </td>
                                                    <td>
                                                        {{ $product->sale_price }}
                                                    </td>
                                                    <td>
                                                        {{ $product->unit }}
                                                    </td>
                                                    <td>
                                                        {{ $product->alert_quantity }}
                                                    </td>
                                                    <td>
                                                        {{ $product->agency_id }}
                                                    </td>
                                                    {{-- <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input user-status" type="checkbox"
                                                                id="user-status-{{ $product->id }}"
                                                                data-id="{{ $product->id }}"
                                                                {{ $product->is_active ? 'checked' : '' }}>
                                                        </div>
                                                    </td> --}}
                                                    {{-- <td>
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-id="{{ $user->id }}">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-user-id="{{ $user->id }}">
                          <i class="bi bi-key"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-user" data-user-id="{{ $user->id }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
