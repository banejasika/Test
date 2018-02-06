<?php

namespace App\Http\Controllers\Job;

use App\Http\Requests;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\HRMail;
use App\Mail\ModeratorMail;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Job;
use Illuminate\Http\Request;

class JobsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $user = $request->user();
        $perPage = 25;
        if (!empty($keyword)) {
            $jobs = Job::where('title', 'LIKE', "%$keyword%")
                ->where( function ($query) use ($user, $request) {
                    if(!strpos($request->url(), 'jobs')) {
                        return $query->where('published', true);
                    }

                    if (isset($user) && $user->user_type == 1) {
                        return $query->where('created_by_user', $user->id);
                    }
                })
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $jobs = Job::where(function ($query) use ($user, $request) {
                if(!strpos($request->url(), 'jobs')) {
                    return $query->where('published', true);
                }

                if (isset($user) && $user->user_type == 1) {
                    return $query->where('created_by_user', $user->id);
                }
            })
                ->paginate($perPage);
        }

        if(strpos($request->url(), 'jobs')) {
            return view('admin.jobs.index', compact('jobs'), compact('user'));
        } else {
            return view('welcome', compact('jobs'), compact('user'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $email = $request->user()->email;
        return view('admin.jobs.create', compact('email'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'email' => 'required|email'
        ]);

        $user = $request->user();
        $requestData = $request->all();
        $requestData['created_by_user'] = $request->user()->id;
        $this->isSubmissionPublished($user, $requestData);

        return redirect('jobs')->with('flash_message', 'Job Submission added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @param $request
     *
     * @return \Illuminate\View\View
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $job = Job::where( function ($query) use ($user) {
        if (isset($user) && $user->user_type == 1) {
            return $query->where('created_by_user', $user->id);
        }
    })->findOrFail($id);

        return view('admin.jobs.show', compact('job'), compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $job = Job::findOrFail($id);

        return view('admin.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'email' => 'required|email'
        ]);

        $requestData = $request->all();
        $job = Job::findOrFail($id);
        $job->update($requestData);

        return redirect('jobs')->with('flash_message', 'Job updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Job::destroy($id);

        return redirect('jobs')->with('flash_message', 'Job Submission deleted!');
    }

    /**
     * Publish or mark as spam job submission
     *
     * @param $id
     *
     * @param $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishOrSpam($id, Request $request)
    {
        $data = $request->all();
        if ($data['method'] == 'publish') {
            Job::where('id', $id)->update(['published' => true]);
            return redirect('jobs')->with('flash_message', 'Job Submission published!');
        }

        if ($data['method'] == 'spam') {
            Job::where('id', $id)->update(['spam' => true]);
            return redirect('jobs')->with('flash_message', 'Job Submission marked as spam!');
        }
    }


    /**
     * Checks hash and publish job submission
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request)
    {

        $data = $request->all();
        $user = User::findOrFail($data['uid']);
        $jobSubmission = Job::where('hash', base64_encode($data['token']))->where('is_hash_active', true)->first();

        if (isset($jobSubmission) && $user->user_type == 2 && $data['method'] == 'publish') {
            $jobSubmission->update(['published' => true, 'is_hash_active' => false]);
            return redirect('/')->with('flash_message', 'Job Submission published!');
        }

        if (isset($jobSubmission) && $user->user_type == 2 && $data['method'] == 'spam') {
            $jobSubmission->update(['spam' => true, 'is_hash_active' => false]);
            return redirect('/')->with('flash_message', 'Job Submission marked as spam!');
        }

        return redirect('/')->with('flash_alert', 'Hash is not correct!');
    }

    /**
     * Sets published value and sends emails based on previous job submissions.
     *
     * @param $user
     *
     * @param $requestData
     */
    private function isSubmissionPublished($user, $requestData)
    {
        $publishedJob = Job::where('email', $requestData['email'])->first();

        if (isset($publishedJob) || $user->user_type == 2) {
            $requestData['published'] = true;
            Job::create($requestData);
        } else {
            $requestData['published'] = false;
            $requestData['hash'] = base64_encode(str_random(20));
            $requestData['is_hash_active'] = true;
            $moderators = User::where('user_type', 2)->get();

            $when = Carbon::now()->addSeconds(5);
            Mail::to($user->email)->later($when, new HRMail());

            $job = Job::create($requestData);

            foreach ($moderators as $moderator) {
                Mail::to($moderator->email)->later($when, new ModeratorMail($job, $moderator));
            }

        }
    }
}
