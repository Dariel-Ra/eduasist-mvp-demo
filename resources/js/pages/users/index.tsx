import { Button } from '@/components/ui/button';

import {

    Table,

    TableBody,

    TableCell,

    TableHead,

    TableHeader,

    TableRow,

} from '@/components/ui/table';

import AppLayout from '@/layouts/app-layout';

import { type BreadcrumbItem, type User } from '@/types';

import { Head, Link, router } from '@inertiajs/react';

import { Edit, Plus, Trash2 } from 'lucide-react';

 

const breadcrumbs: BreadcrumbItem[] = [

    {

        title: 'Usuarios',

        href: '/users',

    },

];

 

interface UsersIndexProps {

    users: {

        data: User[];

        current_page: number;

        last_page: number;

        per_page: number;

        total: number;

    };

    filters: {

        search?: string;

    };

}

 

export default function UsersIndex({ users, filters }: UsersIndexProps) {

    const handleDelete = (userId: number) => {

        if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {

            router.delete(`/users/${userId}`, {

                preserveScroll: true,

            });

        }

    };

 

    const getRoleBadgeColor = (role: string) => {

        const colors: Record<string, string> = {

            sysadmin: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',

            admin: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',

            teacher: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',

            guardian: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',

        };

        return colors[role] || 'bg-gray-100 text-gray-800';

    };

 

    const getStatusBadgeColor = (status: string) => {

        return status === 'active'

            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'

            : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';

    };

 

    return (

        <AppLayout breadcrumbs={breadcrumbs}>

            <Head title="Gestión de Usuarios" />

 

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">

                <div className="flex items-center justify-between">

                    <div>

                        <h1 className="text-2xl font-bold">Gestión de Usuarios</h1>

                        <p className="text-sm text-muted-foreground">

                            Administra los usuarios del sistema

                        </p>

                    </div>

                    <Link href="/users/create">

                        <Button>

                            <Plus className="mr-2 h-4 w-4" />

                            Nuevo Usuario

                        </Button>

                    </Link>

                </div>

 

                <div className="rounded-lg border border-sidebar-border/70 bg-background">

                    <Table>

                        <TableHeader>

                            <TableRow>

                                <TableHead>Nombre</TableHead>

                                <TableHead>Email</TableHead>

                                <TableHead>Teléfono</TableHead>

                                <TableHead>Rol</TableHead>

                                <TableHead>Estado</TableHead>

                                <TableHead className="text-right">Acciones</TableHead>

                            </TableRow>

                        </TableHeader>

                        <TableBody>

                            {users.data.length === 0 ? (

                                <TableRow>

                                    <TableCell

                                        colSpan={6}

                                        className="text-center text-muted-foreground"

                                    >

                                        No se encontraron usuarios

                                    </TableCell>

                                </TableRow>

                            ) : (

                                users.data.map((user) => (

                                    <TableRow key={user.id}>

                                        <TableCell className="font-medium">

                                            {user.first_name} {user.last_name}

                                        </TableCell>

                                        <TableCell>{user.email}</TableCell>

                                        <TableCell>{user.phone || '-'}</TableCell>

                                        <TableCell>

                                            <span

                                                className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${getRoleBadgeColor(user.role)}`}

                                            >

                                                {user.role}

                                            </span>

                                        </TableCell>

                                        <TableCell>

                                            <span

                                                className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${getStatusBadgeColor(user.status)}`}

                                            >

                                                {user.status}

                                            </span>

                                        </TableCell>

                                        <TableCell className="text-right">

                                            <div className="flex justify-end gap-2">

                                                <Link href={`/users/${user.id}/edit`}>

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

                                                    onClick={() => handleDelete(user.id)}

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

 

                {users.last_page > 1 && (

                    <div className="flex items-center justify-between">

                        <p className="text-sm text-muted-foreground">

                            Mostrando {users.data.length} de {users.total} usuarios

                        </p>

                        <div className="flex gap-2">

                            {users.current_page > 1 && (

                                <Link

                                    href={`/users?page=${users.current_page - 1}`}

                                    preserveScroll

                                >

                                    <Button variant="outline" size="sm">

                                        Anterior

                                    </Button>

                                </Link>

                            )}

                            {users.current_page < users.last_page && (

                                <Link

                                    href={`/users?page=${users.current_page + 1}`}

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