<?php

namespace App\Http\Controllers;

use App\Models\CourseSectionStudent;
use App\Models\CourseSection;
use App\Models\Student;
use App\Http\Requests\StoreCourseSectionStudentRequest;
use App\Http\Requests\UpdateCourseSectionStudentRequest;
use App\Http\Resources\CourseSectionStudentResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseSectionStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = CourseSectionStudent::with([

            'courseSection.course',

            'courseSection.teacher.user',

            'student'

        ]);

 

        // Búsqueda por nombre del estudiante o nombre del curso

        if ($search = $request->input('search')) {

            $query->where(function ($q) use ($search) {

                $q->whereHas('student', function ($q) use ($search) {

                    $q->where('first_name', 'like', "%{$search}%")

                      ->orWhere('last_name', 'like', "%{$search}%")

                      ->orWhere('enrollment_code', 'like', "%{$search}%");

                })

                ->orWhereHas('courseSection.course', function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")

                      ->orWhere('code', 'like', "%{$search}%");

                });

            });

        }

 

        // Filtro por sección del curso

        if ($courseSectionId = $request->input('course_section_id')) {

            $query->where('course_section_id', $courseSectionId);

        }

 

        // Filtro por estudiante

        if ($studentId = $request->input('student_id')) {

            $query->where('student_id', $studentId);

        }

 

        // Filtro por estado

        if ($status = $request->input('status')) {

            $query->where('status', $status);

        }

 

        // Ordenamiento

        $sortBy = $request->input('sort_by', 'created_at');

        $sortOrder = $request->input('sort_order', 'desc');

 

        $query->orderBy($sortBy, $sortOrder);

 

        $enrollments = $query->paginate(15)->withQueryString();

 

        return Inertia::render('CourseSectionStudents/Index', [

            'enrollments' => CourseSectionStudentResource::collection($enrollments),

            'filters' => $request->only([

                'search',

                'course_section_id',

                'student_id',

                'status',

                'sort_by',

                'sort_order'

            ]),

            'statuses' => [

                ['value' => 'active', 'label' => 'Activo'],

                ['value' => 'dropped', 'label' => 'Retirado'],

            ],

        ]);

    }

 

    /**

     * Show the form for creating a new resource.

     */

    public function create(): Response

    {

        $courseSections = CourseSection::with(['course', 'teacher.user'])

            ->active()

            ->get()

            ->filter(fn ($section) => $section->hasAvailableSeats());

 

        $students = Student::where('active', true)

            ->orderBy('first_name')

            ->orderBy('last_name')

            ->get();

 

        return Inertia::render('CourseSectionStudents/Create', [

            'course_sections' => $courseSections,

            'students' => $students,

            'statuses' => [

                ['value' => 'active', 'label' => 'Activo'],

                ['value' => 'dropped', 'label' => 'Retirado'],

            ],

        ]);

    }

 

    /**

     * Store a newly created resource in storage.

     */

    public function store(StoreCourseSectionStudentRequest $request)

    {

        $enrollment = CourseSectionStudent::create($request->validated());

 

        $enrollment->load(['courseSection.course', 'student']);

 

        return redirect()->route('course-section-students.show', $enrollment)

            ->with('success', 'Estudiante inscrito en la sección exitosamente.');

    }

 

    /**

     * Display the specified resource.

     */

    public function show(CourseSectionStudent $courseSectionStudent): Response

    {

        $courseSectionStudent->load([

            'courseSection.course',

            'courseSection.teacher.user',

            'student.guardians',

        ]);

 

        return Inertia::render('CourseSectionStudents/Show', [

            'enrollment' => CourseSectionStudentResource::make($courseSectionStudent),

        ]);

    }

 

    /**

     * Show the form for editing the specified resource.

     */

    public function edit(CourseSectionStudent $courseSectionStudent): Response

    {

        $courseSectionStudent->load([

            'courseSection.course',

            'student',

        ]);

 

        $courseSections = CourseSection::with(['course', 'teacher.user'])

            ->active()

            ->get();

 

        $students = Student::where('active', true)

            ->orderBy('first_name')

            ->orderBy('last_name')

            ->get();

 

        return Inertia::render('CourseSectionStudents/Edit', [

            'enrollment' => CourseSectionStudentResource::make($courseSectionStudent),

            'course_sections' => $courseSections,

            'students' => $students,

            'statuses' => [

                ['value' => 'active', 'label' => 'Activo'],

                ['value' => 'dropped', 'label' => 'Retirado'],

            ],

        ]);

    }

 

    /**

     * Update the specified resource in storage.

     */

    public function update(UpdateCourseSectionStudentRequest $request, CourseSectionStudent $courseSectionStudent)

    {

        $courseSectionStudent->update($request->validated());

 

        return redirect()->route('course-section-students.show', $courseSectionStudent)

            ->with('success', 'Inscripción actualizada exitosamente.');

    }

 

    /**

     * Remove the specified resource from storage.

     */

    public function destroy(CourseSectionStudent $courseSectionStudent)

    {

        $courseSectionStudent->delete();

 

        return redirect()->route('course-section-students.index')

            ->with('success', 'Inscripción eliminada exitosamente.');

    }

 

    /**

     * Get all enrollments for a specific course section.

     */

    public function byCourseSection(CourseSection $courseSection)

    {

        $enrollments = CourseSectionStudent::where('course_section_id', $courseSection->id)

            ->with(['student'])

            ->orderBy('created_at', 'desc')

            ->get();

 

        return response()->json([

            'data' => CourseSectionStudentResource::collection($enrollments),

        ]);

    }

 

    /**

     * Get all enrollments for a specific student.

     */

    public function byStudent(Student $student)

    {

        $enrollments = CourseSectionStudent::where('student_id', $student->id)

            ->with(['courseSection.course', 'courseSection.teacher.user'])

            ->orderBy('created_at', 'desc')

            ->get();

 

        return response()->json([

            'data' => CourseSectionStudentResource::collection($enrollments),

        ]);

    }

 

    /**

     * Get active enrollments for a specific student.

     */

    public function activeByStudent(Student $student)

    {

        $enrollments = CourseSectionStudent::where('student_id', $student->id)

            ->where('status', 'active')

            ->with(['courseSection.course', 'courseSection.teacher.user'])

            ->orderBy('created_at', 'desc')

            ->get();

 

        return response()->json([

            'data' => CourseSectionStudentResource::collection($enrollments),

        ]);

    }

 

    /**

     * Bulk enroll students in a course section.

     */

    public function bulkEnroll(Request $request)

    {

        $request->validate([

            'course_section_id' => [

                'required',

                'integer',

                'exists:course_sections,id',

            ],

            'student_ids' => [

                'required',

                'array',

                'min:1',

            ],

            'student_ids.*' => [

                'required',

                'integer',

                'exists:students,id',

            ],

        ]);

 

        $courseSection = CourseSection::findOrFail($request->course_section_id);

 

        if (!$courseSection->active) {

            return response()->json([

                'message' => 'No se puede inscribir estudiantes en una sección inactiva.',

            ], 422);

        }

 

        $enrolled = [];

        $errors = [];

 

        foreach ($request->student_ids as $studentId) {

            // Verificar si el estudiante ya está inscrito

            $exists = CourseSectionStudent::where('course_section_id', $courseSection->id)

                ->where('student_id', $studentId)

                ->exists();

 

            if ($exists) {

                $student = Student::find($studentId);

                $errors[] = [

                    'student_id' => $studentId,

                    'message' => "El estudiante {$student->full_name} ya está inscrito en esta sección.",

                ];

                continue;

            }

 

            // Verificar disponibilidad de cupos

            if (!$courseSection->hasAvailableSeats()) {

                $errors[] = [

                    'student_id' => $studentId,

                    'message' => 'La sección no tiene más cupos disponibles.',

                ];

                break;

            }

 

            $enrollment = CourseSectionStudent::create([

                'course_section_id' => $courseSection->id,

                'student_id' => $studentId,

                'status' => 'active',

            ]);

 

            $enrolled[] = $enrollment->id;

        }

 

        return response()->json([

            'message' => count($enrolled) > 0

                ? sprintf('Se inscribieron %d estudiante(s) exitosamente.', count($enrolled))

                : 'No se inscribió ningún estudiante.',

            'enrolled' => $enrolled,

            'errors' => $errors,

        ], count($enrolled) > 0 ? 201 : 422);

    }

 

    /**

     * Bulk update status for multiple enrollments.

     */

    public function bulkUpdateStatus(Request $request)

    {

        $request->validate([

            'enrollment_ids' => [

                'required',

                'array',

                'min:1',

            ],

            'enrollment_ids.*' => [

                'required',

                'integer',

                'exists:course_section_student,id',

            ],

            'status' => [

                'required',

                'in:active,dropped',

            ],

        ]);

 

        $updated = CourseSectionStudent::whereIn('id', $request->enrollment_ids)

            ->update(['status' => $request->status]);

 

        return response()->json([

            'message' => sprintf('Se actualizaron %d inscripción(es) exitosamente.', $updated),

            'updated' => $updated,

        ]);

    }
}
