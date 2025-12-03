import TeacherController from '@/actions/App/Http/Controllers/TeacherController';
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
        title: 'Profesores',
        href: '/teachers',
    },
    {
        title: 'Crear Profesor',
        href: '/teachers/create',
    },
];

interface AvailableUser {
    id: number;
    name: string;
    email: string;
}

interface TeacherCreateProps {
    availableUsers: AvailableUser[];
}

export default function TeacherCreate({ availableUsers }: TeacherCreateProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Crear Profesor" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold">Crear Nuevo Profesor</h1>
                        <p className="text-sm text-muted-foreground">
                            Completa el formulario para crear un perfil de profesor
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
                    {availableUsers.length === 0 ? (
                        <div className="text-center py-8">
                            <p className="text-muted-foreground">
                                No hay usuarios con rol de profesor disponibles.
                            </p>
                            <p className="text-sm text-muted-foreground mt-2">
                                Por favor, crea primero un usuario con rol "teacher".
                            </p>
                            <Link href="/users/create" className="mt-4 inline-block">
                                <Button>
                                    Crear Usuario
                                </Button>
                            </Link>
                        </div>
                    ) : (
                        <Form
                            {...TeacherController.store.form()}
                            className="space-y-6"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="user_id">
                                            Usuario <span className="text-destructive">*</span>
                                        </Label>
                                        <select
                                            id="user_id"
                                            name="user_id"
                                            required
                                            className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        >
                                            <option value="">Seleccionar usuario</option>
                                            {availableUsers.map((user) => (
                                                <option key={user.id} value={user.id}>
                                                    {user.name} ({user.email})
                                                </option>
                                            ))}
                                        </select>
                                        <p className="text-xs text-muted-foreground">
                                            Solo se muestran usuarios con rol "teacher" que no tienen perfil de profesor
                                        </p>
                                        <InputError message={errors.user_id} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="code">
                                            Código (opcional)
                                        </Label>
                                        <Input
                                            id="code"
                                            name="code"
                                            placeholder="TCH0001"
                                            className="w-full uppercase"
                                            maxLength={50}
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Se generará automáticamente si se deja vacío. Solo mayúsculas y números.
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
                                            {processing ? 'Creando...' : 'Crear Profesor'}
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
