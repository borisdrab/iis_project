import { Head, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import '../../../css/admin-users.css';

type UserFromBackend = {
    id: number;
    meno: string;
    email: string;
    aktivny: boolean;
    is_user: boolean;
    is_moderator: boolean;
    is_admin: boolean;
};

type User = UserFromBackend & {
    rola: 'Uzivatel' | 'Moderator' | 'Administrator';
};

export default function ListOfUsers() {
    const { auth } = usePage<{
        auth: {
            user: {
                id: number;
                meno: string;
            } | null;
        };
    }>().props;

    const [users, setUsers] = useState<User[]>([]);
    const [loading, setLoading] = useState(true);
    const [savingId, setSavingId] = useState<number | null>(null);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    function getRole(user: UserFromBackend): User['rola'] {
        if (user.is_admin) {
            return 'Administrator';
        }

        if (user.is_moderator) {
            return 'Moderator';
        }

        return 'Uzivatel';
    }

    function getXsrfToken() {
        const cookie = document.cookie
            .split('; ')
            .find((item) => item.startsWith('XSRF-TOKEN='));

        if (!cookie) {
            return '';
        }

        return decodeURIComponent(cookie.split('=')[1]);
    }

    async function loadUsers() {
        try {
            setLoading(true);
            setError('');

            const response = await fetch('/admin/users', {
                headers: {
                    Accept: 'application/json',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Používateľov sa nepodarilo načítať.');
            }

            const data: UserFromBackend[] = await response.json();

            setUsers(
                data.map((user) => ({
                    ...user,
                    rola: getRole(user),
                }))
            );
        } catch {
            setError('Používateľov sa nepodarilo načítať.');
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        void loadUsers();
    }, []);

    function changeRole(id: number, rola: User['rola']) {
        setUsers((currentUsers) =>
            currentUsers.map((user) =>
                user.id === id
                    ? {
                          ...user,
                          rola,
                      }
                    : user
            )
        );
    }

    async function saveRole(user: User) {
        try {
            setSavingId(user.id);
            setMessage('');
            setError('');

            const response = await fetch(`/admin/users/${user.id}`, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getXsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    meno: user.meno,
                    email: user.email,
                    rola: user.rola,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors?.rola) {
                    setError(data.errors.rola[0]);
                } else {
                    setError(
                        data.message ?? 'Rolu sa nepodarilo zmeniť.'
                    );
                }

                await loadUsers();
                return;
            }

            setMessage(data.message);
            await loadUsers();
        } catch {
            setError('Rolu sa nepodarilo zmeniť.');
        } finally {
            setSavingId(null);
        }
    }

    async function toggleActive(user: User) {
        try {
            setSavingId(user.id);
            setMessage('');
            setError('');

            const response = await fetch(
                `/admin/users/${user.id}/toggle-active`,
                {
                    method: 'PATCH',
                    headers: {
                        Accept: 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                    },
                    credentials: 'same-origin',
                }
            );

            const data = await response.json();

            if (!response.ok) {
                setError(
                    data.message ??
                        'Stav používateľa sa nepodarilo zmeniť.'
                );
                return;
            }

            setMessage(data.message);
            await loadUsers();
        } catch {
            setError('Stav používateľa sa nepodarilo zmeniť.');
        } finally {
            setSavingId(null);
        }
    }

    return (
        <>
            <Head title="Správa používateľov" />

            <main className="admin-users-container">
                <h1>Správa používateľov</h1>

                <p className="admin-users-description">
                    Správa rolí a stavu používateľských účtov.
                </p>

                {message && (
                    <p className="admin-users-success">
                        {message}
                    </p>
                )}

                {error && (
                    <p className="admin-users-error">
                        {error}
                    </p>
                )}

                {loading ? (
                    <p>Načítavam používateľov...</p>
                ) : (
                    <div className="admin-users-table-wrapper">
                        <table className="admin-users-table">
                            <thead>
                                <tr>
                                    <th>Meno</th>
                                    <th>Email</th>
                                    <th>Rola</th>
                                    <th>Stav</th>
                                    <th>Akcie</th>
                                </tr>
                            </thead>

                            <tbody>
                                {users.map((user) => {
                                    const isCurrentUser =
                                        auth.user?.id === user.id;

                                    return (
                                        <tr key={user.id}>
                                            <td>
                                                {user.meno}

                                                {isCurrentUser && (
                                                    <span className="admin-users-you">
                                                        {' '}
                                                        (ty)
                                                    </span>
                                                )}
                                            </td>

                                            <td>{user.email}</td>

                                            <td>
                                                <select
                                                    value={user.rola}
                                                    disabled={isCurrentUser}
                                                    onChange={(event) =>
                                                        changeRole(
                                                            user.id,
                                                            event.target
                                                                .value as User['rola']
                                                        )
                                                    }
                                                >
                                                    <option value="Uzivatel">
                                                        Používateľ
                                                    </option>

                                                    <option value="Moderator">
                                                        Moderátor
                                                    </option>

                                                    <option value="Administrator">
                                                        Administrátor
                                                    </option>
                                                </select>
                                            </td>

                                            <td>
                                                <span
                                                    className={
                                                        user.aktivny
                                                            ? 'user-active'
                                                            : 'user-inactive'
                                                    }
                                                >
                                                    {user.aktivny
                                                        ? 'Aktívny'
                                                        : 'Neaktívny'}
                                                </span>
                                            </td>

                                            <td>
                                                <div className="admin-users-actions">
                                                    <button
                                                        type="button"
                                                        className="admin-save-button"
                                                        disabled={
                                                            isCurrentUser ||
                                                            savingId ===
                                                                user.id
                                                        }
                                                        onClick={() =>
                                                            saveRole(user)
                                                        }
                                                    >
                                                        Uložiť
                                                    </button>

                                                    <button
                                                        type="button"
                                                        className={
                                                            user.aktivny
                                                                ? 'admin-disable-button'
                                                                : 'admin-enable-button'
                                                        }
                                                        disabled={
                                                            isCurrentUser ||
                                                            savingId ===
                                                                user.id
                                                        }
                                                        onClick={() =>
                                                            toggleActive(user)
                                                        }
                                                    >
                                                        {user.aktivny
                                                            ? 'Deaktivovať'
                                                            : 'Aktivovať'}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}
            </main>
        </>
    );
}