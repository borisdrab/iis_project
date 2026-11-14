import { Link, usePage } from '@inertiajs/react'
import '../../css/app-layout.css'

type Osoba = {
    id: number;
    meno: string;
    email: string;
    aktivny: boolean;
};

type PageProps = {
    auth: {
        user: Osoba | null;
    };
};


export default function AppLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const {auth} = usePage<PageProps>().props;

    return (
        <div className="app-wrapper">
            <header className="app-header">
                <div className="app-header-content">
                    <Link href="/" className="app-logo">
                        Events
                    </Link>

                    <nav className="app-nav">
                        <Link href="/udalosti">Udalosti</Link>

                        {auth.user && (
                            <Link href="/kalendar">
                                Moj kalendar
                            </Link>
                        )}

                        {auth.user && (
                            <Link href="/udalosti/create">
                                Vytvoriť udalosť
                            </Link>
                        )}
                    </nav>

                    <div className="app-user">
                        {auth.user ? (
                            <>
                                <span>{auth.user.meno}</span>

                                <Link href="/dashboard">
                                    Profil
                                </Link>
                            </>
                        ) : (
                            <>
                                <Link href="/login">
                                    Prihlasit
                                </Link>

                                <Link href="/register">
                                    Registrovat
                                </Link>
                            </>
                        )}
                    </div>
                </div>
            </header>

            <main className="app-content">
                {children}
            </main>

            <footer className="app-footer">
                IS pre spravu udalosti
            </footer>
        </div>
    );
}
