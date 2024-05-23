<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Course;

class CourseController extends Controller
{

    public function all(Request $request){
      try {
        $courses = Course::with([ 'registrations.student'])->get();
        return response()->json(['success' => true, 'courses' => $courses], 200); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > course all", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when querying all courses.',
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
            'title' => 'required|string|max:255',
        ]);
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors(),
            ], 422);
        }
        DB::beginTransaction();
        $course = Course::create($params);
        DB::commit();
        $responseData = [
          'status' => true,
          'mensage' => 'Your course has been successfully registered!',
          'dados' => $params,
          'course' => $course
        ];
        return response()->json(['success' => true, 'responseData' => $responseData], 200);
      } catch (\Exception $e) {
        DB::rollback();
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > course create", $errorData);
        $responseData = [
            'status' => false,
            'mensage' => 'An error occurred when registering a course.',
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function show(Request $request, $id){
      try {
        $course = Course::with([ 'registrations.student'])->find($id);
        if(isset($course)){
          return response()->json(['success' => true, 'course' => $course], 200); 
        }
        return response()->json(['success' => false, 'course' => $course, 'message' => "No courses were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
        ];
        Log::channel("controller")->error("Erro > course show", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when querying the course with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
    public function update(Request $request, $id){
      try {
        $course = Course::find($id);
        if(isset($course)){
          $params = $request->all();
          $course->update($params);
          return response()->json(['success' => true, 'course' => $course], 200); 
        }
        return response()->json(['success' => false, 'course' => $course, 'message' => "No courses were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("controller")->error("Erro > course update", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred when updating the course with id $id.",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
    
    public function delete(Request $request){
      try {
        $params = $request->all();
        $id = $params['id'];
        $course = Course::find($id);
        if(isset($course)){
          $course->delete();
          return response()->json(['success' => true, 'message' => "course $course->name has been successfully deleted!"], 200); 
        }
        return response()->json(['success' => false, 'course' => $course, 'message' => "No courses were found with the id $id."], 404); 
      } catch (\Exception $e) {
        $errorData = [
          'arquivo' => $e->getFile(),
          'linha' => $e->getLine(),
          'erro' => $e->getMessage(),
          'data' => $request->all(),
        ];
        Log::channel("controller")->error("Erro > course delete", $errorData);
        $responseData = [
            'status' => false,
            'message' => "An error occurred while deleting course",
            'error' => $errorData,
        ];
        return response()->json(['success' => false, 'responseData' => $responseData], 500);
      }
    }
  
  }
