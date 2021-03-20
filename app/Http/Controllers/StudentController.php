<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Student::with('teacher')->get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" onclick="editFunc(' . $row->id . ')" data-original-title="Edit" class="edit btn btn-success edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0);" id="delete-student" onclick="deleteFunc(' . $row->id . ')" data-toggle="tooltip" data-original-title="Delete" class="delete btn btn-danger">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $teachers = Teacher::pluck('name', 'id');
        return view('students', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $studentId = $request->id;
            $student = Student::updateOrCreate(['id' => $studentId], [
                'name' => ucwords($request->name),
                'gender' => $request->gender,
                'age' => $request->age,
                'teacher_id' => $request->teacher_id,
            ]);
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $student = Student::where($where)->first();
        return Response()->json($student);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $student = Student::where('id', $request->id)->delete();
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }
}
