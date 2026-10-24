<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\RealEstate\Http\Requests\StoreAgentApplicationRequest;
use Botble\RealEstate\Models\AgentApplication;
use Botble\RealEstate\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Theme;

class AgentApplicationApplyController extends Controller
{
    /**
     * @return string
     */
    public function create()
    {
        $categories = Category::where('status', BaseStatusEnum::PUBLISHED)->orderBy('name')->get(['id', 'name']);

        return Theme::scope('real-estate.agent-application.create', compact('categories'))->render();
    }

    /**
     * @param StoreAgentApplicationRequest $request
     * @return RedirectResponse
     */
    public function store(StoreAgentApplicationRequest $request): RedirectResponse
    {
        $file = $request->file('resume');
        $resumePath = $file->store('agent-applications', 'local');

        $application = AgentApplication::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'education_level' => $request->input('education_level'),
            'years_of_experience' => $request->input('years_of_experience'),
            'resume_path' => $resumePath,
            'resume_original_name' => $file->getClientOriginalName(),
        ]);

        $application->categories()->sync($request->input('category_ids', []));

        return redirect()
            ->route('public.agent-application.create')
            ->with('success_msg', __('Your application has been submitted. Our team will review it and get in touch if there is a fit.'));
    }
}
