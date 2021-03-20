<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\ScoreDetail;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Score::with(['scoreDetails', 'term', 'student'])->get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" onclick="editFunc(' . $row->id . ')" data-original-title="Edit" class="edit btn btn-success edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0);" id="delete-student" onclick="deleteFunc(' . $row->id . ')" data-toggle="tooltip" data-original-title="Delete" class="delete btn btn-danger">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $subjects = Subject::orderBy('order', 'ASC')->pluck('name', 'id');
        $students = Student::pluck('name', 'id');
        $terms = Term::pluck('name', 'id');
        return view('scores', compact('subjects', 'students', 'terms'));
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
            $scoreId = $request->score_id;
            $scoreArray = $request->get('scores');
            if (!empty($scoreArray)) {
                if (empty($scoreId)) {
                    $scoreObject = Score::where('term_id', $request->term_id)->where('student_id', $request->student_id)->first();
                    if (!empty($scoreObject)) {
                        $scoreId = $scoreObject->id;
                    }
                }

                $score = Score::updateOrCreate(['id' => $scoreId], [
                    'term_id' => $request->term_id,
                    'student_id' => $request->student_id,
                ]);

                if (empty($scoreId)) {
                    $scoreId = $score->id;
                }

                foreach ($scoreArray as $subjectId => $score) {
                    $score = ScoreDetail::updateOrCreate(['score_id' => $scoreId, 'subject_id' => $subjectId], [
                        'subject_id' => $subjectId,
                        'score_id' => $scoreId,
                        'mark' => $score,
                    ]);
                }
            }
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $where = array('id' => $request->id);
        $score = Score::with('scoreDetails')->where($where)->first();
        return Response()->json($score);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Score  $score
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $score = Score::where('id', $request->id)->delete();
            $scoreDetails = ScoreDetail::where('score_id', $request->id)->delete();
            return Response()->json(array('success' => true));
        } catch (\Exception $e) {
            return response()->json(array('success' => false, 'message' => 'Operation Failed, please contact admin'));
        }
    }
}
