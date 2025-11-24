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
import { Input } from '@/components/ui/input';
import { useState } from 'react';

interface Course {
    id: number;
    name: string;
    code: string | null;
    description: string | null;
    grade_level: string | null;
    active: boolean;
    created_at: string;
}

interface PaginatedData {
    data: Course[];
    links: any[];
    meta: any;
}

interface Props {
    courses: PaginatedData;
    filters: {
        search?: string;
        grade_level?: string;
        active?: boolean;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Cursos',
        href: '/courses',
    },
];

export default function Index({ courses, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/courses', { search }, { preserveState: true });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Cursos" />

            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Cursos</h1>
                        <p className="text-muted-foreground">
                            Gestiona los cursos del sistema
                        </p>
                    </div>
                    <Link href="/courses/create">
                        <Button>
                            <span className="mr-2">+</span>
                            Nuevo Curso
                        </Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Filtros</CardTitle>
                        <CardDescription>
                            Busca y filtra cursos
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSearch} className="flex gap-2">
                            <Input
                                placeholder="Buscar por nombre, código o nivel..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="max-w-md"
                            />
                            <Button type="submit">Buscar</Button>
                            {filters.search && (
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => {
                                        setSearch('');
                                        router.get('/courses');
                                    }}
                                >
                                    Limpiar
                                </Button>
                            )}
                        </form>
                    </CardContent>
                </Card>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {courses.data.map((course) => (
                        <Card key={course.id} className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="flex items-start justify-between">
                                    <div className="flex-1">
                                        <CardTitle className="text-lg">
                                            {course.name}
                                        </CardTitle>
                                        {course.code && (
                                            <p className="text-sm text-muted-foreground mt-1">
                                                {course.code}
                                            </p>
                                        )}
                                    </div>
                                    <Badge variant={course.active ? 'default' : 'secondary'}>
                                        {course.active ? 'Activo' : 'Inactivo'}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {course.description && (
                                    <p className="text-sm text-muted-foreground line-clamp-2 mb-3">
                                        {course.description}
                                    </p>
                                )}
                                {course.grade_level && (
                                    <p className="text-sm mb-3">
                                        <span className="font-medium">Nivel:</span> {course.grade_level}
                                    </p>
                                )}
                                <div className="flex gap-2">
                                    <Link href={`/courses/${course.id}`}>
                                        <Button variant="outline" size="sm">
                                            Ver
                                        </Button>
                                    </Link>
                                    <Link href={`/courses/${course.id}/edit`}>
                                        <Button variant="outline" size="sm">
                                            Editar
                                        </Button>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {courses.data.length === 0 && (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <p className="text-muted-foreground">
                                No se encontraron cursos
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
