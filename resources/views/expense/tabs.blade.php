@extends('layouts.app')

@section('title', 'Dépenses & Types de Dépense')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-between">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <i class="bi bi-cash-stack"></i> Dépenses & Types
            </li>
        </ol>
    </nav>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="expenseTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab" aria-controls="expenses" aria-selected="true">
                <i class="bi bi-cash-stack me-1"></i> Dépenses
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="types-tab" data-bs-toggle="tab" data-bs-target="#types" type="button" role="tab" aria-controls="types" aria-selected="false">
                <i class="bi bi-tags me-1"></i> Types de Dépense
            </button>
        </li>
    </ul>
    <div class="tab-content" id="expenseTabsContent">
        <div class="tab-pane fade show active" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
            @include('expense.partials.expense-list')
        </div>
        <div class="tab-pane fade" id="types" role="tabpanel" aria-labelledby="types-tab">
            @include('expense.partials.type-list')
        </div>
    </div>
</div>
@endsection