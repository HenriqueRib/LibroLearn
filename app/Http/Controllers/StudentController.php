<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Carbon\Carbon;

class StudentController extends Controller
{

    public function index(Request $request){
        try {
        $students = Student::with('registrations.course')->get();
        $statistics = [];
        foreach ($students as $student) {
            $age = Carbon::parse($student->birth_date)->age;
            $courseName = $student->registrations->first()->course->title ?? 'Nenhum curso';
            $sex = $student->sex;
            $ageGroup = $this->getAgeGroup($age);

            if (!isset($statistics[$courseName])) {
                $statistics[$courseName] = [];
            }

            if (!isset($statistics[$courseName][$sex])) {
                $statistics[$courseName][$sex] = [
                    'menor que 15 anos' => 0,
                    'entre 15 e 18 anos' => 0,
                    'entre 19 e 24 anos' => 0,
                    'entre 25 e 30 anos' => 0,
                    'maior que 30 anos' => 0,
                ];
            }

            $statistics[$courseName][$sex][$ageGroup]++;
        }

        return response()->json(['success' => true, 'statistics' => $statistics], 200);
   
        } catch (\Exception $e) {
          $errorData = [
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine(),
            'erro' => $e->getMessage(),
          ];
          Log::channel("controller")->error("Erro > student index", $errorData);
          $responseData = [
              'status' => false,
              'mensage' => 'An error occurred when querying index stores.',
              'error' => $errorData,
          ];
          return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
      }

    public function all(Request $request){
      try {
        $students = Student::with(['registrations.course'])->get();

        return response()->json(['success' => true, 'students' => $students], 200); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > student all", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when querying all stores.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function create(Request $request)
    {
      try {
        $params = $request->all();
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required',
            'birth_date' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors(),
            ], 422);
        }
    
        DB::beginTransaction();
        $student = Student::create($params);
        DB::commit();
        $responseData = [
          'status' => true,
          'mensage' => 'Your student has been successfully registered!',
          'dados' => $params,
          'student' => $student
        ];
        return response()->json(['success' => true, 'responseData' => $responseData], 200);
      } catch (\Exception $e) {
        DB::rollback();
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > student create", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when registering a student.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function show(Request $request, $id){
      try {
        $student = Student::with(['registrations.course'])->find($id);
        if(isset($student)){
          return response()->json(['success' => true, 'student' => $student], 200); 
        }
        return response()->json(['success' => false, 'student' => $student, 'message' => "No students were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > student show", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when querying the student with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function update(Request $request, $id){
      try {
        $student = Student::find($id);
        if(isset($student)){
          $params = $request->all();
          $student->update($params);
          return response()->json(['success' => true, 'student' => $student], 200); 
        }
        return response()->json(['success' => false, 'student' => $student, 'message' => "No students were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("controller")->error("Erro > student update", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when updating the student with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
    
    public function delete(Request $request){
      try {
        $params = $request->all();
        $id = $params['id'];
        $student = Student::find($id);
        if(isset($student)){
          $student->delete();
          return response()->json(['success' => true, 'message' => "student $student->name has been successfully deleted!"], 200); 
        }
        return response()->json(['success' => false, 'student' => $student, 'message' => "No students were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("controller")->error("Erro > student delete", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred while deleting student",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }

    public function search(Request $request){
        try {
          $params = $request->all();
          $query = Student::select('*')->with(['registrations.course'])->orderBy('id', 'desc');
          if (isset($params['name']) != null) {
              $query->where('name', 'like', '%' . $params['name'] . '%');
          }

          if ( isset($params['email']) != null) {
            $query->where('email', 'like', '%' . $params['email'] . '%');
        }
        $student = $query->get();

          return response()->json(['success' => true, 'message' => "student  has been successfully search!", 'student' => $student,], 200); 
        } catch (\Exception $e) {
          $errorData = [
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine(),
            'erro' => $e->getMessage(),
            'data' => $request->all(),
          ];
          Log::channel("controller")->error("Erro > student search", $errorData);
          $responseData = [
              'status' => false,
              'message' => "An error occurred while search student",
              'error' => $errorData,
          ];
          return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
      }

      private function getAgeGroup($age)
      {
          if ($age < 15) {
              return 'menor que 15 anos';
          } elseif ($age >= 15 && $age <= 18) {
              return 'entre 15 e 18 anos';
          } elseif ($age >= 19 && $age <= 24) {
              return 'entre 19 e 24 anos';
          } elseif ($age >= 25 && $age <= 30) {
              return 'entre 25 e 30 anos';
          } else {
              return 'maior que 30 anos';
          }
      }
  }
