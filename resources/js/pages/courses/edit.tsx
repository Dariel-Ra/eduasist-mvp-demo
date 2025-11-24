import { Head, Link, useForm } from '@inertiajs/react';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/input-error';

interface Course {
    id: number;
    name: string;
    code: string | null;
    description: string | null;
    grade_level: string | null;
    active: boolean;
}

interface Props {
    course: Course;
}

export default function Edit({ course }: Props) {
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
        {
            title: 'Editar',
            href: `/courses/${course.id}/edit`,
        },
    ];

    const { data, setData, put, processing, errors } = useForm({
        name: course.name || '',
        code: course.code || '',
        description: course.description || '',
        grade_level: course.grade_level || '',
        active: course.active,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/courses/${course.id}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Editar ${course.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Editar Curso</h1>
                        <p className="text-muted-foreground">
                            Actualiza la información del curso
                        </p>
                    </div>
                    <Link href={`/courses/${course.id}`}>
                        <Button variant="outline">Cancelar</Button>
                    </Link>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Información del Curso</CardTitle>
                        <CardDescription>
                            Modifica los datos del curso
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="name">Nombre *</Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="Ej: Matemáticas"
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="code">Código</Label>
                                <Input
                                    id="code"
                                    value={data.code}
                                    onChange={(e) => setData('code', e.target.value.toUpperCase())}
                                    placeholder="Ej: MAT-101"
                                />
                                <InputError message={errors.code} />
                                <p className="text-sm text-muted-foreground">
                                    Solo mayúsculas, números y guiones
                                </p>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="description">Descripción</Label>
                                <textarea
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    placeholder="Descripción del curso..."
                                    className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                    rows={4}
                                />
                                <InputError message={errors.description} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="grade_level">Nivel de Grado</Label>
                                <Input
                                    id="grade_level"
                                    value={data.grade_level}
                                    onChange={(e) => setData('grade_level', e.target.value)}
                                    placeholder="Ej: 1ro Secundaria, 2do Secundaria"
                                />
                                <InputError message={errors.grade_level} />
                            </div>

                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="active"
                                    checked={data.active}
                                    onCheckedChange={(checked) => setData('active', checked as boolean)}
                                />
                                <Label htmlFor="active" className="cursor-pointer">
                                    Curso activo
                                </Label>
                            </div>

                            <div className="flex gap-2 pt-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Guardando...' : 'Guardar Cambios'}
                                </Button>
                                <Link href={`/courses/${course.id}`}>
                                    <Button type="button" variant="outline">
                                        Cancelar
                                    </Button>
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
