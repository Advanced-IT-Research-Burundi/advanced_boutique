<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $clients = Client::all();

        return view('client.index', [
            'clients' => $clients,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('client.create');
    }

    public function store(ClientStoreRequest $request): Response
    {
        $client = Client::create($request->validated());

        $request->session()->flash('client.id', $client->id);

        return redirect()->route('clients.index');
    }

    public function show(Request $request, Client $client): Response
    {
        return view('client.show', [
            'client' => $client,
        ]);
    }

    public function edit(Request $request, Client $client): Response
    {
        return view('client.edit', [
            'client' => $client,
        ]);
    }

    public function update(ClientUpdateRequest $request, Client $client): Response
    {
        $client->update($request->validated());

        $request->session()->flash('client.id', $client->id);

        return redirect()->route('clients.index');
    }

    public function destroy(Request $request, Client $client): Response
    {
        $client->delete();

        return redirect()->route('clients.index');
    }
}
