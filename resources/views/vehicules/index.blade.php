@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h1>Liste des véhicules</h1>
        <a href="{{ route('vehicules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau véhicule
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Année</th>
                            <th>Immatriculation</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicules as $vehicule)
                            <tr>

                                <td>{{ $vehicule->id }}</td>
                                <td>{{ $vehicule->brand }}</td>
                                <td>{{ $vehicule->model }}</td>
                                <td>{{ $vehicule->year }}</td>
                                <td>{{ $vehicule->immatriculation }}</td>
                                <td>
                                    <span class="badge
                                        @if($vehicule->statut == 'disponible') bg-success
                                        @elseif($vehicule->statut == 'en_location') bg-warning
                                        @else bg-danger
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $vehicule->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vehicules.show', $vehicule) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vehicules.edit', $vehicule) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('vehicules.destroy', $vehicule) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun véhicule enregistré</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $vehicules->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
