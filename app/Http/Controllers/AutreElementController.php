<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutreElementStoreRequest;
use App\Http\Requests\AutreElementUpdateRequest;
use App\Http\Resources\AutreElementCollection;
use App\Http\Resources\AutreElementResource;
use App\Models\AutreElement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutreElementController extends Controller
{
    public function index(Request $request)
    {
        $autreElements = AutreElement::latest()->paginate();
        
        return   sendResponse($autreElements, Response::HTTP_OK);
    }
    
    public function store(AutreElementStoreRequest $request)
    {

        $pathURL  = '';

        // upload document if exists
        if ($request->hasFile('document')) {
            $imageName = time().'.'.$request->document->extension();
            $path = $request->file('document')->move(public_path('documents/others'), $imageName);
            $pathURL = 'documents/others/'.$imageName;
        } 


        $autreElement = AutreElement::create([
            'date' => $request->date,
            'libelle' => $request->libelle,
            'emplacement' => $request->emplacement,
            'quantite' => $request->quantite,
            'valeur' => $request->valeur,
            'devise' => $request->devise,
            'type_element' => $request->type_element,
            'reference' => $request->reference,
            'observation' => $request->observation,
            'exchange_rate' => $request->exchange_rate,
            'document' => $pathURL ,
            
        ]);
        
        return sendResponse(new AutreElementResource($autreElement), Response::HTTP_CREATED);
    }
    
    public function show(Request $request, AutreElement $autreElement)
    {
        return new AutreElementResource($autreElement);
    }
    
    public function update(AutreElementUpdateRequest $request, AutreElement $autreElement)
    {
        $pathURL  = '';

        // upload document if exists
        if ($request->hasFile('document')) {
            $imageName = time().'.'.$request->document->extension();
                 $request->file('document')->move(public_path('documents/others'), $imageName);
            $pathURL = 'documents/others/'.$imageName;
        }
        $autreElement->update([
            'date' => $request->date,
            'libelle' => $request->libelle,
            'emplacement' => $request->emplacement,
            'quantite' => $request->quantite,
            'valeur' => $request->valeur,
            'devise' => $request->devise,
            'type_element' => $request->type_element,
            'reference' => $request->reference,
            'observation' => $request->observation,
            'exchange_rate' => $request->exchange_rate,
            'document' => $pathURL ? $pathURL : $autreElement->document,
        ]);
        
        return sendResponse(new AutreElementResource($autreElement), Response::HTTP_OK);
    }
    
    public function destroy(Request $request, AutreElement $autreElement)
    {
        $autreElement->delete();
        
        return response()->noContent();
    }
}
