import { Head, Link } from '@inertiajs/react';
import '../../../css/kalendar.css';

type Miesto = {
    id: number;
    adresa: string;
};

type Kategorie = {
    id: number;
    typ: string;
};

type Udalost = {
    id: number;
    oznacenie: string;
    datum_od: string;
    cas_od: string;
    datum_do: string;
    cas_do: string;
    popis: string | null;
    miesto: Miesto | null;
    kategoria: Kategorie | null;
};

type Registracia = {
    id: number;
    udalost: Udalost;
};

type Props = {
    registracie: Registracia[];
};

export default function Index({ registracie }: Props) {
    const sortedRegistracie = [...registracie].sort((a, b) => {
        const dateA = new Date(
            `${a.udalost.datum_od}T${a.udalost.cas_od}`
        );

        const dateB = new Date(
            `${b.udalost.datum_od}T${b.udalost.cas_od}`
        );

        return dateA.getTime() - dateB.getTime();
    });

    const groupedRegistracie = sortedRegistracie.reduce<
        Record<string, Registracia[]>
    >((groups, registracia) => {
        const datum = registracia.udalost.datum_od;

        if (!groups[datum]) {
            groups[datum] = [];
        }

        groups[datum].push(registracia);

        return groups;
    }, {});

    function formatDate(date: string) {
        return new Date(`${date}T00:00:00`).toLocaleDateString('sk-SK', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    }

    function formatTime(time: string) {
        return time.slice(0, 5);
    }

    return (
        <>
            <Head title="Môj kalendár" />

            <main className="kalendar-container">
                <div className="kalendar-header">
                    <div>
                        <p className="kalendar-subtitle">
                            Moje udalosti
                        </p>

                        <h1>Môj kalendár</h1>

                        <p className="kalendar-description">
                            Prehľad udalostí, na ktoré si prihlásený.
                        </p>
                    </div>

                    <div className="kalendar-count">
                        <strong>{registracie.length}</strong>
                        <span>
                            {registracie.length === 1
                                ? 'udalosť'
                                : 'udalostí'}
                        </span>
                    </div>
                </div>

                {registracie.length === 0 ? (
                    <section className="kalendar-empty">
                        <h2>Zatiaľ tu nič nemáš</h2>

                        <p>
                            Nie si prihlásený na žiadnu udalosť.
                        </p>

                        <Link
                            href="/"
                            className="kalendar-events-link"
                        >
                            Prezrieť udalosti
                        </Link>
                    </section>
                ) : (
                    <div className="kalendar-agenda">
                        {Object.entries(groupedRegistracie).map(
                            ([datum, registracieDna]) => (
                                <section
                                    className="kalendar-day"
                                    key={datum}
                                >
                                    <div className="kalendar-date">
                                        <span className="kalendar-date-day">
                                            {new Date(
                                                `${datum}T00:00:00`
                                            ).getDate()}
                                        </span>

                                        <div>
                                            <strong>
                                                {formatDate(datum)}
                                            </strong>

                                            <span>
                                                {registracieDna.length}{' '}
                                                {registracieDna.length === 1
                                                    ? 'udalosť'
                                                    : 'udalostí'}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="kalendar-day-events">
                                        {registracieDna.map(
                                            (registracia) => {
                                                const udalost =
                                                    registracia.udalost;

                                                return (
                                                    <Link
                                                        key={registracia.id}
                                                        href={`/udalosti/${udalost.id}`}
                                                        className="kalendar-event"
                                                    >
                                                        <div className="kalendar-event-time">
                                                            <strong>
                                                                {formatTime(
                                                                    udalost.cas_od
                                                                )}
                                                            </strong>

                                                            <span>
                                                                –
                                                            </span>

                                                            <span>
                                                                {formatTime(
                                                                    udalost.cas_do
                                                                )}
                                                            </span>
                                                        </div>

                                                        <div className="kalendar-event-content">
                                                            {udalost.kategoria && (
                                                                <span className="kalendar-category">
                                                                    {
                                                                        udalost
                                                                            .kategoria
                                                                            .typ
                                                                    }
                                                                </span>
                                                            )}

                                                            <h2>
                                                                {
                                                                    udalost.oznacenie
                                                                }
                                                            </h2>

                                                            {udalost.miesto && (
                                                                <p className="kalendar-place">
                                                                    {
                                                                        udalost
                                                                            .miesto
                                                                            .adresa
                                                                    }
                                                                </p>
                                                            )}

                                                            {udalost.popis && (
                                                                <p className="kalendar-event-description">
                                                                    {
                                                                        udalost.popis
                                                                    }
                                                                </p>
                                                            )}
                                                        </div>

                                                        <div className="kalendar-arrow">
                                                            →
                                                        </div>
                                                    </Link>
                                                );
                                            }
                                        )}
                                    </div>
                                </section>
                            )
                        )}
                    </div>
                )}
            </main>
        </>
    );
}