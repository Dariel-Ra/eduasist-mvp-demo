import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';

interface CourseSection {
    id: number;
    section: string;
    classroom: string | null;
    max_students: number | null;
}

interface Course {
    id: number;
    name: string;
    code: string | null;
    description: string | null;
    grade_level: string | null;
    active: boolean;
    created_at: string;
    sections?: CourseSection[];
}

interface Props {
    course: Course;
}

export default function Show({ course }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
        {
            title: 'Cursos',
            href: '/courses',
        },
        {
            title: course.name,
            href: `/courses/${course.id}`,
        },
    ];

    const handleDelete = () => {
        router.delete(`/courses/${course.id}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={course.name} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">{course.name}</h1>
                        {course.code && (
                            <p className="text-muted-foreground">{course.code}</p>
                        )}
                    </div>
                    <div className="flex gap-2">
                        <Link href={`/courses/${course.id}/edit`}>
                            <Button variant="outline">Editar</Button>
                        </Link>
                        <AlertDialog>
                            <AlertDialogTrigger asChild>
                                <Button variant="destructive">Eliminar</Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>¿Estás seguro?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        Esta acción no se puede deshacer. El curso será eliminado
                                        permanentemente del sistema.
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                                    <AlertDialogAction onClick={handleDelete}>
                                        Eliminar
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Información del Curso</CardTitle>
                            <CardDescription>
                                Detalles generales del curso
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div>
                                <p className="text-sm font-medium text-muted-foreground">Nombre</p>
                                <p className="text-base">{course.name}</p>
                            </div>

                            {course.code && (
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Código</p>
                                    <p className="text-base font-mono">{course.code}</p>
                                </div>
                            )}

                            {course.grade_level && (
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">
                                        Nivel de Grado
                                    </p>
                                    <p className="text-base">{course.grade_level}</p>
                                </div>
                            )}

                            <div>
                                <p className="text-sm font-medium text-muted-foreground">Estado</p>
                                <Badge variant={course.active ? 'default' : 'secondary'} className="mt-1">
                                    {course.active ? 'Activo' : 'Inactivo'}
                                </Badge>
                            </div>

                            {course.description && (
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">
                                        Descripción
                                    </p>
                                    <p className="text-base text-muted-foreground">
                                        {course.description}
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Secciones</CardTitle>
                            <CardDescription>
                                Secciones asociadas a este curso
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {course.sections && course.sections.length > 0 ? (
                                <div className="space-y-2">
                                    {course.sections.map((section) => (
                                        <div
                                            key={section.id}
                                            className="flex items-center justify-between rounded-lg border p-3"
                                        >
                                            <div>
                                                <p className="font-medium">Sección {section.section}</p>
                                                {section.classroom && (
                                                    <p className="text-sm text-muted-foreground">
                                                        Aula: {section.classroom}
                                                    </p>
                                                )}
                                            </div>
                                            {section.max_students && (
                                                <Badge variant="outline">
                                                    Max: {section.max_students}
                                                </Badge>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    No hay secciones asociadas a este curso
                                </p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
