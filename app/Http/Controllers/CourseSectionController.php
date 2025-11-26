<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Course;
use App\Models\Teacher;
use App\Enums\ScheduleDay;
use App\Http\Requests\StoreCourseSectionRequest;
use App\Http\Requests\UpdateCourseSectionRequest;
use App\Http\Resources\CourseSectionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sections = CourseSection::query()
            ->with(['course', 'teacher'])
            ->when($request->search, function ($query, $search) {
                $query->where('section', 'like', "%{$search}%")
                      ->orWhere('classroom', 'like', "%{$search}%")
                      ->orWhereHas('course', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('teacher', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->when($request->course_id, function ($query, $courseId) {
                $query->where('course_id', $courseId);
            })
            ->when($request->teacher_id, function ($query, $teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->when($request->active !== null, function ($query) use ($request) {
                $query->where('active', $request->boolean('active'));
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('CourseSections/Index', [
            'sections' => CourseSectionResource::collection($sections),
            'filters' => $request->only(['search', 'course_id', 'teacher_id', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('CourseSections/Create', [
            'courses' => Course::active()->get(['id', 'name', 'code']),
            'teachers' => Teacher::active()->get(['id', 'name', 'email']),
            'scheduleDays' => $this->getScheduleDaysForFrontend(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseSectionRequest $request)
    {
        $request->load(['course', 'teacher', 'students']);
        // // Create the section from validated data
        // $validated = $request->validated();
        // $courseSection = CourseSection::create($validated);

        // // If students were passed, sync them (expecting array of ids)
        // if ($request->has('students')) {
        //     $studentIds = collect($request->input('students'))->map(fn($s) => is_array($s) && isset($s['id']) ? $s['id'] : $s)->filter()->values()->all();
        //     if (!empty($studentIds)) {
        //         $courseSection->students()->sync($studentIds);
        //     }
        // }

        // $courseSection->load(['course', 'teacher', 'students']);
        return Inertia::render('CourseSections/Show', [
            'section' => new CourseSectionResource($request),
            // 'section' => CourseSectionResource::make($courseSection),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseSection $courseSection): Response
    {
        $courseSection->load(['course', 'teacher', 'students']);

        return Inertia::render('CourseSections/Show', [
            'section' => new CourseSectionResource($courseSection),
            //'section' => CourseSectionResource::make($courseSection),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseSection $courseSection): Response
    {
        
        return Inertia::render('CourseSections/Edit', [
            'section' => new CourseSectionResource($courseSection),
            // 'section' => CourseSectionResource::make($courseSection),
            'courses' => Course::active()->get(['id', 'name', 'code']),
            'teachers' => Teacher::active()->get(['id', 'name', 'email']),
            'scheduleDays' => $this->getScheduleDaysForFrontend(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseSectionRequest $request, CourseSection $courseSection)
    {
        $courseSection->update($request->validated());

        return redirect()
            ->route('course-sections.index')
            ->with('success', 'Sección actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSection $courseSection)
    {
        // Verificar si tiene estudiantes inscritos
        if ($courseSection->students()->exists()) {
            return back()->with('error', 'No se puede eliminar una sección con estudiantes inscritos.');
        }

        $courseSection->delete();
        return redirect()
            ->route('course-sections.index')
            ->with('success', 'Sección eliminada exitosamente.');
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(CourseSection $courseSection)
    {
        $courseSection->update(['active' => !$courseSection->active]);

        return back()->with('success', $courseSection->active ? 'Sección activada.' : 'Sección desactivada.');
    }

    /**
     * Get sections in session now
     */
    public function currentSessions()
    {
        $sections = CourseSection::active()
            ->with(['course', 'teacher'])
            ->get()
            ->filter(fn($section) => $section->isInSession())
            ->values();

        return Inertia::render('CourseSections/CurrentSessions', [
            'sections' => CourseSectionResource::collection($sections),
        ]);
    }

    /**
     * Get schedule days formatted for frontend
     */
    private function getScheduleDaysForFrontend(): array
    {
        return collect(ScheduleDay::cases())->map(function ($day) {
            return [
                'value' => $day->value,
                'label' => $day->label(),
                'short_label' => $day->shortLabel(),
            ];
        })->toArray();
    }
}
