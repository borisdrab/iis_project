import { Head, Link, useForm } from '@inertiajs/react';
import type { SyntheticEvent } from 'react';
import '../../../css/auth.css';

export default function Login() {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        email: '',
        password: '',
    });

    function submit(event: SyntheticEvent<HTMLFormElement>) {
        event.preventDefault();

        post('/login');
    }

    return (
        <>
            <Head title="Prihlásenie" />

            <main className="auth-page">
                <div className="auth-card">
                    <div className="auth-heading">
                        <h1>Prihlásenie</h1>
                        <p>
                            Prihlás sa do svojho účtu.
                        </p>
                    </div>

                    <form onSubmit={submit} className="auth-form">
                        <div className="auth-field">
                            <label htmlFor="email">
                                E-mail
                            </label>

                            <input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(event) =>
                                    setData('email', event.target.value)
                                }
                                required
                                autoFocus
                                placeholder="email@example.com"
                            />

                            {errors.email && (
                                <p className="auth-error">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div className="auth-field">
                            <label htmlFor="password">
                                Heslo
                            </label>

                            <input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(event) =>
                                    setData('password', event.target.value)
                                }
                                required
                                placeholder="Heslo"
                            />

                            {errors.password && (
                                <p className="auth-error">
                                    {errors.password}
                                </p>
                            )}
                        </div>

                        <button
                            type="submit"
                            className="auth-button"
                            disabled={processing}
                        >
                            {processing
                                ? 'Prihlasujem...'
                                : 'Prihlásiť sa'}
                        </button>
                    </form>

                    <p className="auth-switch">
                        Nemáte účet?{' '}
                        <Link href="/register">
                            Zaregistrujte sa
                        </Link>
                    </p>

                    <Link href="/" className="auth-back">
                        ← Späť na úvod
                    </Link>
                </div>
            </main>
        </>
    );
}