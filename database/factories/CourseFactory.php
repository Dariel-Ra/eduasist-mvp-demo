<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Materias comunes del nivel secundario en República Dominicana
        $courses = [
            'Matemáticas',
            'Lengua Española',
            'Ciencias Sociales',
            'Ciencias de la Naturaleza',
            'Inglés',
            'Francés',
            'Educación Física',
            'Educación Artística',
            'Formación Integral Humana y Religiosa',
            'Informática',
            'Física',
            'Química',
            'Biología',
            'Historia Dominicana',
            'Geografía',
            'Literatura',
        ];

        // Niveles de grado del nivel secundario (1ro a 6to)
        $gradeLevels = [
            '1ro Secundaria',
            '2do Secundaria',
            '3ro Secundaria',
            '4to Secundaria',
            '5to Secundaria',
            '6to Secundaria',
        ];

        $courseName = fake()->randomElement($courses);

        // Descripciones genéricas basadas en la materia
        $descriptions = [
            'Matemáticas' => 'Estudio de conceptos matemáticos, álgebra, geometría y cálculo.',
            'Lengua Española' => 'Desarrollo de competencias lingüísticas y literarias en español.',
            'Ciencias Sociales' => 'Estudio de la sociedad, historia, geografía y ciudadanía.',
            'Ciencias de la Naturaleza' => 'Exploración de fenómenos naturales, biología, física y química.',
            'Inglés' => 'Desarrollo de habilidades comunicativas en inglés.',
            'Francés' => 'Desarrollo de habilidades comunicativas en francés.',
            'Educación Física' => 'Promoción de la actividad física y el desarrollo motor.',
            'Educación Artística' => 'Desarrollo de la creatividad y expresión artística.',
            'Formación Integral Humana y Religiosa' => 'Formación en valores, ética y espiritualidad.',
            'Informática' => 'Introducción a la tecnología y programación.',
            'Física' => 'Estudio de las leyes y principios que rigen el universo físico.',
            'Química' => 'Estudio de la composición, estructura y propiedades de la materia.',
            'Biología' => 'Estudio de los seres vivos y sus procesos vitales.',
            'Historia Dominicana' => 'Estudio de la historia y desarrollo de la República Dominicana.',
            'Geografía' => 'Estudio de la superficie terrestre, clima y recursos naturales.',
            'Literatura' => 'Análisis de obras literarias y desarrollo del pensamiento crítico.',
        ];

        return [
            'name' => $courseName,
            'code' => 'CRS-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => $descriptions[$courseName] ?? fake()->sentence(10),
            'grade_level' => fake()->randomElement($gradeLevels),
            'active' => fake()->boolean(90), // 90% de cursos activos
        ];
    }

    /**
     * Indicate that the course should not have a code.
     */
    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => null,
        ]);
    }

    /**
     * Indicate that the course should not have a description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }

    /**
     * Indicate that the course should not have a grade level.
     */
    public function withoutGradeLevel(): static
    {
        return $this->state(fn (array $attributes) => [
            'grade_level' => null,
        ]);
    }

    /**
     * Indicate that the course should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the course should be for a specific grade level.
     */
    public function forGradeLevel(string $gradeLevel): static
    {
        return $this->state(fn (array $attributes) => [
            'grade_level' => $gradeLevel,
        ]);
    }

    /**
     * Indicate that the course should have a specific code.
     */
    public function withCode(string $code): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => $code,
        ]);
    }
}
