import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Edit, FilterX, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profesores',
        href: '/teachers',
    },
];

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
}

interface TeachersIndexProps {
    teachers: {
        data: Teacher[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters: {
        search?: string;
        specialty?: string;
        sort_by?: string;
        sort_order?: string;
    };
    specialties: string[];
}

export default function TeachersIndex({
    teachers,
    filters,
    specialties,
}: TeachersIndexProps) {
    const [search, setSearch] = useState(filters.search || '');
    const [specialty, setSpecialty] = useState(filters.specialty || '');
    const [sortBy, setSortBy] = useState(filters.sort_by || 'created_at');
    const [sortOrder, setSortOrder] = useState(filters.sort_order || 'desc');

    // Debounce search
    useEffect(() => {
        const timeout = setTimeout(() => {
            applyFilters();
        }, 300);

        return () => clearTimeout(timeout);
    }, [search, specialty, sortBy, sortOrder]);

    const applyFilters = () => {
        router.get(
            '/teachers',
            {
                search: search || undefined,
                specialty: specialty || undefined,
                sort_by: sortBy,
                sort_order: sortOrder,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const clearFilters = () => {
        setSearch('');
        setSpecialty('');
        setSortBy('created_at');
        setSortOrder('desc');
        router.get('/teachers', {}, { preserveState: false });
    };

    const handleDelete = (teacherId: number) => {
        if (confirm('¿Estás seguro de que deseas eliminar este profesor?')) {
            router.delete(`/teachers/${teacherId}`, {
                preserveScroll: true,
            });
        }
    };

    const hasActiveFilters = search || specialty || sortBy !== 'created_at' || sortOrder !== 'desc';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Gestión de Profesores" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Gestión de Profesores</h1>
                        <p className="text-sm text-muted-foreground">
                            Administra los profesores del sistema
                        </p>
                    </div>
                    <Link href="/teachers/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Nuevo Profesor
                        </Button>
                    </Link>
                </div>

                {/* Filters and Search */}
                <div className="rounded-lg border border-sidebar-border/70 bg-background p-4">
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        {/* Search */}
                        <div className="lg:col-span-2">
                            <div className="relative">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    type="text"
                                    placeholder="Buscar por nombre, código o especialidad..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="pl-10"
                                />
                            </div>
                        </div>

                        {/* Specialty Filter */}
                        <div>
                            <select
                                value={specialty}
                                onChange={(e) => setSpecialty(e.target.value)}
                                className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="">Todas las especialidades</option>
                                {specialties.map((s) => (
                                    <option key={s} value={s}>
                                        {s}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Sort By */}
                        <div className="flex gap-2">
                            <select
                                value={sortBy}
                                onChange={(e) => setSortBy(e.target.value)}
                                className="flex-1 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="created_at">Fecha de creación</option>
                                <option value="name">Nombre</option>
                                <option value="code">Código</option>
                                <option value="specialty">Especialidad</option>
                            </select>
                            <select
                                value={sortOrder}
                                onChange={(e) => setSortOrder(e.target.value)}
                                className="rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="asc">↑ Asc</option>
                                <option value="desc">↓ Desc</option>
                            </select>
                        </div>
                    </div>

                    {hasActiveFilters && (
                        <div className="mt-4 flex items-center gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={clearFilters}
                            >
                                <FilterX className="mr-2 h-4 w-4" />
                                Limpiar filtros
                            </Button>
                            <p className="text-sm text-muted-foreground">
                                {teachers.total} resultado(s) encontrado(s)
                            </p>
                        </div>
                    )}
                </div>

                <div className="rounded-lg border border-sidebar-border/70 bg-background">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Código</TableHead>
                                <TableHead>Nombre</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Especialidad</TableHead>
                                <TableHead className="text-right">Acciones</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {teachers.data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={5}
                                        className="text-center text-muted-foreground"
                                    >
                                        No se encontraron profesores
                                    </TableCell>
                                </TableRow>
                            ) : (
                                teachers.data.map((teacher) => (
                                    <TableRow key={teacher.id}>
                                        <TableCell className="font-medium">
                                            {teacher.code}
                                        </TableCell>
                                        <TableCell>
                                            {teacher.user.first_name} {teacher.user.last_name}
                                        </TableCell>
                                        <TableCell>{teacher.user.email}</TableCell>
                                        <TableCell>{teacher.specialty || '-'}</TableCell>
                                        <TableCell className="text-right">
                                            <div className="flex justify-end gap-2">
                                                <Link href={`/teachers/${teacher.id}`}>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        className="h-8 w-8"
                                                    >
                                                        <Search className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Link href={`/teachers/${teacher.id}/edit`}>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        className="h-8 w-8"
                                                    >
                                                        <Edit className="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    className="h-8 w-8 text-destructive hover:text-destructive"
                                                    onClick={() => handleDelete(teacher.id)}
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>

                {teachers.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-sm text-muted-foreground">
                            Mostrando {teachers.data.length} de {teachers.total} profesores
                        </p>
                        <div className="flex gap-2">
                            {teachers.current_page > 1 && (
                                <Link
                                    href={`/teachers?page=${teachers.current_page - 1}`}
                                    preserveScroll
                                >
                                    <Button variant="outline" size="sm">
                                        Anterior
                                    </Button>
                                </Link>
                            )}
                            {teachers.current_page < teachers.last_page && (
                                <Link
                                    href={`/teachers?page=${teachers.current_page + 1}`}
                                    preserveScroll
                                >
                                    <Button variant="outline" size="sm">
                                        Siguiente
                                    </Button>
                                </Link>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
