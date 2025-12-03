import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Edit, Mail, User } from 'lucide-react';

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
        phone?: string | null;
    };
    sections?: any[];
    attendances?: any[];
}

interface TeacherShowProps {
    teacher: Teacher;
}

export default function TeacherShow({ teacher }: TeacherShowProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Profesores',
            href: '/teachers',
        },
        {
            title: `${teacher.user.first_name} ${teacher.user.last_name}`,
            href: `/teachers/${teacher.id}`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${teacher.user.first_name} ${teacher.user.last_name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">
                            {teacher.user.first_name} {teacher.user.last_name}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Información detallada del profesor
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Link href="/teachers">
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Volver
                            </Button>
                        </Link>
                        <Link href={`/teachers/${teacher.id}/edit`}>
                            <Button>
                                <Edit className="mr-2 h-4 w-4" />
                                Editar
                            </Button>
                        </Link>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {/* Información del Profesor */}
                    <div className="rounded-lg border border-sidebar-border/70 bg-background p-6">
                        <h2 className="text-lg font-semibold mb-4">Información del Profesor</h2>
                        <div className="space-y-4">
                            <div>
                                <p className="text-sm text-muted-foreground">Código</p>
                                <p className="font-medium">{teacher.code}</p>
                            </div>
                            <div>
                                <p className="text-sm text-muted-foreground">Especialidad</p>
                                <p className="font-medium">{teacher.specialty || 'No especificada'}</p>
                            </div>
                        </div>
                    </div>

                    {/* Información del Usuario */}
                    <div className="rounded-lg border border-sidebar-border/70 bg-background p-6">
                        <h2 className="text-lg font-semibold mb-4">Información del Usuario</h2>
                        <div className="space-y-4">
                            <div className="flex items-center gap-2">
                                <User className="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p className="text-sm text-muted-foreground">Nombre completo</p>
                                    <p className="font-medium">
                                        {teacher.user.first_name} {teacher.user.last_name}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <Mail className="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p className="text-sm text-muted-foreground">Email</p>
                                    <p className="font-medium">{teacher.user.email}</p>
                                </div>
                            </div>
                            {teacher.user.phone && (
                                <div>
                                    <p className="text-sm text-muted-foreground">Teléfono</p>
                                    <p className="font-medium">{teacher.user.phone}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Secciones */}
                <div className="rounded-lg border border-sidebar-border/70 bg-background p-6">
                    <h2 className="text-lg font-semibold mb-4">Secciones Asignadas</h2>
                    {teacher.sections && teacher.sections.length > 0 ? (
                        <div className="space-y-2">
                            {teacher.sections.map((section: any, index: number) => (
                                <div
                                    key={index}
                                    className="p-3 rounded-md border border-sidebar-border/50"
                                >
                                    <p className="font-medium">Sección {index + 1}</p>
                                    <p className="text-sm text-muted-foreground">
                                        {/* Aquí puedes agregar más detalles de la sección */}
                                        Información de la sección
                                    </p>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-muted-foreground">
                            No tiene secciones asignadas actualmente
                        </p>
                    )}
                </div>

                {/* Estadísticas */}
                <div className="rounded-lg border border-sidebar-border/70 bg-background p-6">
                    <h2 className="text-lg font-semibold mb-4">Estadísticas</h2>
                    <div className="grid gap-4 md:grid-cols-3">
                        <div className="p-4 rounded-md bg-muted/50">
                            <p className="text-sm text-muted-foreground">Secciones</p>
                            <p className="text-2xl font-bold">
                                {teacher.sections?.length || 0}
                            </p>
                        </div>
                        <div className="p-4 rounded-md bg-muted/50">
                            <p className="text-sm text-muted-foreground">Asistencias Registradas</p>
                            <p className="text-2xl font-bold">
                                {teacher.attendances?.length || 0}
                            </p>
                        </div>
                        <div className="p-4 rounded-md bg-muted/50">
                            <p className="text-sm text-muted-foreground">Estado</p>
                            <p className="text-2xl font-bold text-green-600">Activo</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
