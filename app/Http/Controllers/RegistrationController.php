<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Registration;

class RegistrationController extends Controller
{

    public function all(Request $request){
        try {
        $registrations = Registration::with(['student', 'course'])->get();
          return response()->json(['success' => true, 'registrations' => $registrations], 200); 
        } catch (\Exception $e) {
          $errorData = [
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine(),
            'erro' => $e->getMessage(),
          ];
          Log::channel("controller")->error("Erro > registration all", $errorData);
          $responseData = [
              'status' => false,
              'mensage' => 'An error occurred when querying all registrations.',
              'error' => $errorData,
          ];
          return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
      }

    public function create(Request $request)
    {
      try {
        $params = $request->all();
        DB::beginTransaction();
        $registration = Registration::create($params);
        DB::commit();
        $responseData = [
          'status' => true,
          'mensage' => 'Your registration has been successfully registered!',
          'dados' => $params,
          'registration' => $registration
        ];
        return response()->json(['success' => true, 'responseData' => $responseData], 200);
      } catch (\Exception $e) {
        DB::rollback();
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > registration create", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when registering a registration.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function show(Request $request, $id){
      try {
        $registration = Registration::with(['student', 'course'])->find($id);

        if(isset($registration)){
          return response()->json(['success' => true, 'registration' => $registration], 200); 
        }
        return response()->json(['success' => false, 'registration' => $registration, 'message' => "No registrations were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > registration show", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when querying the registration with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function update(Request $request, $id){
      try {
        $registration = Registration::find($id);
        if(isset($registration)){
          $params = $request->all();
          $registration->update($params);
          return response()->json(['success' => true, 'registration' => $registration], 200); 
        }
        return response()->json(['success' => false, 'registration' => $registration, 'message' => "No registrations were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("controller")->error("Erro > registration update", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when updating the registration with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
    
    public function delete(Request $request){
      try {
        $params = $request->all();
        $id = $params['id'];
        $registration = Registration::find($id);
        if(isset($registration)){
          $registration->delete();
          return response()->json(['success' => true, 'message' => "registration $registration->name has been successfully deleted!"], 200); 
        }
        return response()->json(['success' => false, 'registration' => $registration, 'message' => "No registrations were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("controller")->error("Erro > registration delete", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred while deleting registration",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
  }

