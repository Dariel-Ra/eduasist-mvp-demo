import TeacherController from '@/actions/App/Http/Controllers/TeacherController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

interface Teacher {
    id: number;
    user_id: number;
    code: string;
    specialty: string | null;
    user: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
    };
}

interface TeacherEditProps {
    teacher: Teacher;
}

export default function TeacherEdit({ teacher }: TeacherEditProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Profesores',
            href: '/teachers',
        },
        {
            title: 'Editar Profesor',
            href: `/teachers/${teacher.id}/edit`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Editar ${teacher.user.first_name} ${teacher.user.last_name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Editar Profesor</h1>
                        <p className="text-sm text-muted-foreground">
                            Actualiza la información del profesor
                        </p>
                    </div>
                    <Link href="/teachers">
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Volver
                        </Button>
                    </Link>
                </div>

                <div className="rounded-lg border border-sidebar-border/70 bg-background p-6">
                    <Form
                        {...TeacherController.update.form(teacher.id)}
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="user">
                                        Usuario
                                    </Label>
                                    <Input
                                        id="user"
                                        value={`${teacher.user.first_name} ${teacher.user.last_name} (${teacher.user.email})`}
                                        disabled
                                        className="w-full bg-muted"
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        El usuario asociado no puede ser modificado
                                    </p>
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="code">
                                        Código <span className="text-destructive">*</span>
                                    </Label>
                                    <Input
                                        id="code"
                                        name="code"
                                        defaultValue={teacher.code}
                                        required
                                        className="w-full uppercase"
                                        maxLength={50}
                                    />
                                    <p className="text-xs text-muted-foreground">
                                        Solo mayúsculas y números
                                    </p>
                                    <InputError message={errors.code} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="specialty">
                                        Especialidad (opcional)
                                    </Label>
                                    <Input
                                        id="specialty"
                                        name="specialty"
                                        defaultValue={teacher.specialty || ''}
                                        placeholder="Matemáticas, Física, Literatura..."
                                        className="w-full"
                                        maxLength={100}
                                    />
                                    <InputError message={errors.specialty} />
                                </div>

                                <div className="flex justify-end gap-4">
                                    <Link href="/teachers">
                                        <Button type="button" variant="outline">
                                            Cancelar
                                        </Button>
                                    </Link>
                                    <Button type="submit" disabled={processing}>
                                        {processing ? 'Actualizando...' : 'Actualizar Profesor'}
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </div>
        </AppLayout>
    );
}
