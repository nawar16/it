<?php

namespace App\Http\Controllers\web;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Mark;
use App\User;
use App\Course;
use App\Events\newMark;
use App\Http\Resources\mark as MarkResource;
use App\Http\Resources\user as UserResource;
use App\Http\Resources\course as CourseResource;

class Markcontroller extends Controller
{

    public function index()
    {
        $marks = Mark::paginate(15);
        return MarkResource::collection($marks);
    }

    public function store(Request $request)
    {
        $mark = $request->isMethod('put') ? Mark::where('StudentID',$request->StudentID)->firstOrFail() : new Mark;
        
                $mark->StudentID = $request->input('StudentID');
                $mark->CourseName = $request->input('CourseName');
                $mark->LabHomeworkMark = $request->input('LabHomeworkMark');
                $mark->LabExamMark = $request->input('LabExamMark');
                $mark->FinalExamMark = $request->input('FinalExamMark');
                $std = User::findOrFail($mark->StudentID);
                
                // New Pusher instance with config data
                $pusher = new \Pusher\Pusher(config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'), 
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options'));
   
                // Enable pusher logging, used an anonymous class and the Monolog
                $pusher->set_logger(new class {
                    public function log($msg)
                      {
                          \Log::info($msg);
                      }
                });
                //  data to send to Pusher
                $data = ['text' => 'Hello '.$std->name.', you\'ve got new mark'];

                if($mark->save()){
                    $pusher->trigger( 'std_'.$mark->StudentID, 'my-event', $data);
                    return redirect(route('doctor.dashboard'), $mark);
                }
    }

    public function show($universityID)
    {
        $std = User::where('universityID',$universityID)->first();
        $marks = Mark::where('StudentID',$std->id)->paginate(15);
        return view('std.grades', $marks);
    }

    public function destroy($id)
    {
        $stdM = Mark::where('StudentID',$id)->firstOrFail();
        if($stdM->delete()){
            return new MarkResource($stdM);
        }
    }

    public function destroy1($name)
    {
        $crsM = Mark::where('CourseName',$name)->firstOrFail();
        if($crsM->delete()){
            return new MarkResource($crsM);
        }
    }
}
