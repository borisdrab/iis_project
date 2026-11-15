import { Head, Link, useForm } from '@inertiajs/react';
import type { SyntheticEvent } from 'react';
import '../../../css/auth.css';

export default function Register() {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        meno: '',
        email: '',
        kontakt: '',
        vek: '',
        pohlavie: '',
        password: '',
        password_confirmation: '',
    });

    function submit(event: SyntheticEvent<HTMLFormElement>) {
        event.preventDefault();

        post('/register');
    }

    return (
        <>
            <Head title="Registrácia" />

            <main className="auth-page">
                <div className="auth-card auth-card-wide">
                    <div className="auth-heading">
                        <h1>Vytvoriť účet</h1>
                        <p>
                            Vyplňte svoje údaje a vytvorte si účet.
                        </p>
                    </div>

                    <form onSubmit={submit} className="auth-form">
                        <div className="auth-field">
                            <label htmlFor="meno">
                                Meno
                            </label>

                            <input
                                id="meno"
                                type="text"
                                value={data.meno}
                                onChange={(event) =>
                                    setData('meno', event.target.value)
                                }
                                required
                            />

                            {errors.meno && (
                                <p className="auth-error">
                                    {errors.meno}
                                </p>
                            )}
                        </div>

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
                            />

                            {errors.email && (
                                <p className="auth-error">
                                    {errors.email}
                                </p>
                            )}
                        </div>

                        <div className="auth-field">
                            <label htmlFor="kontakt">
                                Kontakt
                            </label>

                            <input
                                id="kontakt"
                                type="text"
                                value={data.kontakt}
                                onChange={(event) =>
                                    setData('kontakt', event.target.value)
                                }
                                placeholder="Nepovinné"
                            />

                            {errors.kontakt && (
                                <p className="auth-error">
                                    {errors.kontakt}
                                </p>
                            )}
                        </div>

                        <div className="auth-row">
                            <div className="auth-field">
                                <label htmlFor="vek">
                                    Vek
                                </label>

                                <input
                                    id="vek"
                                    type="number"
                                    min="0"
                                    max="110"
                                    value={data.vek}
                                    onChange={(event) =>
                                        setData('vek', event.target.value)
                                    }
                                    placeholder="Nepovinné"
                                />

                                {errors.vek && (
                                    <p className="auth-error">
                                        {errors.vek}
                                    </p>
                                )}
                            </div>

                            <div className="auth-field">
                                <label htmlFor="pohlavie">
                                    Pohlavie
                                </label>

                                <select
                                    id="pohlavie"
                                    value={data.pohlavie}
                                    onChange={(event) =>
                                        setData(
                                            'pohlavie',
                                            event.target.value
                                        )
                                    }
                                >
                                    <option value="">
                                        Neuvedené
                                    </option>
                                    <option value="Muž">
                                        Muž
                                    </option>
                                    <option value="Žena">
                                        Žena
                                    </option>
                                </select>

                                {errors.pohlavie && (
                                    <p className="auth-error">
                                        {errors.pohlavie}
                                    </p>
                                )}
                            </div>
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
                            />

                            {errors.password && (
                                <p className="auth-error">
                                    {errors.password}
                                </p>
                            )}
                        </div>

                        <div className="auth-field">
                            <label htmlFor="password_confirmation">
                                Zopakujte heslo
                            </label>

                            <input
                                id="password_confirmation"
                                type="password"
                                value={data.password_confirmation}
                                onChange={(event) =>
                                    setData(
                                        'password_confirmation',
                                        event.target.value
                                    )
                                }
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            className="auth-button"
                            disabled={processing}
                        >
                            {processing
                                ? 'Vytvára sa účet...'
                                : 'Registrovať sa'}
                        </button>
                    </form>

                    <p className="auth-switch">
                        Už máte účet?{' '}
                        <Link href="/login">
                            Prihláste sa
                        </Link>
                    </p>

                    <Link href="/" className="auth-back">
                        ← Späť
                    </Link>
                </div>
            </main>
        </>
    );
}