import UserController from '@/actions/App/Http/Controllers/UserController';

import InputError from '@/components/input-error';

import { Button } from '@/components/ui/button';

import { Input } from '@/components/ui/input';

import { Label } from '@/components/ui/label';

import AppLayout from '@/layouts/app-layout';

import { type BreadcrumbItem } from '@/types';

import { Form, Head, Link } from '@inertiajs/react';

import { ArrowLeft } from 'lucide-react';

 

const breadcrumbs: BreadcrumbItem[] = [

    {

        title: 'Usuarios',

        href: '/users',

    },

    {

        title: 'Crear Usuario',

        href: '/users/create',

    },

];

 

interface UserCreateProps {

    availableRoles: string[];

    statuses: string[];

}

 

export default function UserCreate({

    availableRoles,

    statuses,

}: UserCreateProps) {

    return (

        <AppLayout breadcrumbs={breadcrumbs}>

            <Head title="Crear Usuario" />

 

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">

                <div className="flex items-center justify-between">

                    <div>

                        <h1 className="text-2xl font-bold">Crear Nuevo Usuario</h1>

                        <p className="text-sm text-muted-foreground">

                            Completa el formulario para crear un nuevo usuario

                        </p>

                    </div>

                    <Link href="/users">

                        <Button variant="outline">

                            <ArrowLeft className="mr-2 h-4 w-4" />

                            Volver

                        </Button>

                    </Link>

                </div>

 

                <div className="rounded-lg border border-sidebar-border/70 bg-background p-6">

                    <Form

                        {...UserController.store.form()}

                        className="space-y-6"

                    >

                        {({ processing, errors }) => (

                            <>

                                <div className="grid gap-4 sm:grid-cols-2">

                                    <div className="grid gap-2">

                                        <Label htmlFor="first_name">

                                            Nombre <span className="text-destructive">*</span>

                                        </Label>

                                        <Input

                                            id="first_name"

                                            name="first_name"

                                            required

                                            autoComplete="given-name"

                                            placeholder="Nombre"

                                            className="w-full"

                                        />

                                        <InputError message={errors.first_name} />

                                    </div>

 

                                    <div className="grid gap-2">

                                        <Label htmlFor="last_name">

                                            Apellido <span className="text-destructive">*</span>

                                        </Label>

                                        <Input

                                            id="last_name"

                                            name="last_name"

                                            required

                                            autoComplete="family-name"

                                            placeholder="Apellido"

                                            className="w-full"

                                        />

                                        <InputError message={errors.last_name} />

                                    </div>

                                </div>

 

                                <div className="grid gap-2">

                                    <Label htmlFor="email">

                                        Correo Electrónico <span className="text-destructive">*</span>

                                    </Label>

                                    <Input

                                        id="email"

                                        name="email"

                                        type="email"

                                        required

                                        autoComplete="email"

                                        placeholder="correo@ejemplo.com"

                                        className="w-full"

                                    />

                                    <InputError message={errors.email} />

                                </div>

 

                                <div className="grid gap-2">

                                    <Label htmlFor="phone">

                                        Teléfono

                                    </Label>

                                    <Input

                                        id="phone"

                                        name="phone"

                                        type="tel"

                                        autoComplete="tel"

                                        placeholder="+1234567890"

                                        className="w-full"

                                    />

                                    <InputError message={errors.phone} />

                                </div>

 

                                <div className="grid gap-4 sm:grid-cols-2">

                                    <div className="grid gap-2">

                                        <Label htmlFor="role">

                                            Rol <span className="text-destructive">*</span>

                                        </Label>

                                        <select

                                            id="role"

                                            name="role"

                                            required

                                            className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"

                                        >

                                            <option value="">Seleccionar rol</option>

                                            {availableRoles.map((role) => (

                                                <option key={role} value={role}>

                                                    {role.charAt(0).toUpperCase() + role.slice(1)}

                                                </option>

                                            ))}

                                        </select>

                                        <InputError message={errors.role} />

                                    </div>

 

                                    <div className="grid gap-2">

                                        <Label htmlFor="status">

                                            Estado <span className="text-destructive">*</span>

                                        </Label>

                                        <select

                                            id="status"

                                            name="status"

                                            defaultValue="active"

                                            className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"

                                        >

                                            {statuses.map((status) => (

                                                <option key={status} value={status}>

                                                    {status.charAt(0).toUpperCase() + status.slice(1)}

                                                </option>

                                            ))}

                                        </select>

                                        <InputError message={errors.status} />

                                    </div>

                                </div>

 

                                <div className="grid gap-4 sm:grid-cols-2">

                                    <div className="grid gap-2">

                                        <Label htmlFor="password">

                                            Contraseña <span className="text-destructive">*</span>

                                        </Label>

                                        <Input

                                            id="password"

                                            name="password"

                                            type="password"

                                            required

                                            autoComplete="new-password"

                                            placeholder="••••••••"

                                            className="w-full"

                                        />

                                        <InputError message={errors.password} />

                                    </div>

 

                                    <div className="grid gap-2">

                                        <Label htmlFor="password_confirmation">

                                            Confirmar Contraseña <span className="text-destructive">*</span>

                                        </Label>

                                        <Input

                                            id="password_confirmation"

                                            name="password_confirmation"

                                            type="password"

                                            required

                                            autoComplete="new-password"

                                            placeholder="••••••••"

                                            className="w-full"

                                        />

                                    </div>

                                </div>

 

                                <div className="flex justify-end gap-4">

                                    <Link href="/users">

                                        <Button type="button" variant="outline">

                                            Cancelar

                                        </Button>

                                    </Link>

                                    <Button type="submit" disabled={processing}>

                                        {processing ? 'Creando...' : 'Crear Usuario'}

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