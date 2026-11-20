import { Head, router } from '@inertiajs/react';
import '../../../css/moderation.css';

type Osoba = {
    id: number;
    meno: string;
};

type MiestoInfo = {
    id: number;
    adresa: string;
    typ: string;
};

type KategoriaInfo = {
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
    kapacita: number | null;
    popis: string | null;

    miesto: MiestoInfo | null;
    kategoria: KategoriaInfo | null;
    zakladatel: Osoba | null;
};

type Miesto = {
    id: number;
    adresa: string;
    typ: string;
    popis: string | null;

    navrhovatel: Osoba | null;
};

type Kategorie = {
    id: number;
    typ: string;

    rodic: KategoriaInfo | null;
    navrhovatel: Osoba | null;
};

type Props = {
    udalosti: Udalost[];
    miesta: Miesto[];
    schvaleneMiesta: Miesto[];
    kategorie: Kategorie[];
    schvaleneKategorie: Kategorie[];
};

export default function Moderation({
    udalosti,
    miesta,
    schvaleneMiesta,
    kategorie,
    schvaleneKategorie,
}: Props) {
    function schvalUdalost(id: number) {
        router.patch(`/moderator/udalosti/${id}/schvalit`);
    }

    function zamietniUdalost(id: number) {
        router.patch(`/moderator/udalosti/${id}/zamietnut`);
    }

    function schvalMiesto(id: number) {
        router.patch(`/moderator/miesta/${id}/schvalit`);
    }

    function zamietniMiesto(id: number) {
        router.patch(`/moderator/miesta/${id}/zamietnut`);
    }

    function vymazMiesto(id: number) {
        if (window.confirm('Odstrániť toto miesto ?')) {
            router.delete(`/moderator/miesta/${id}`, {
                onError: (errors) => {
                    window.alert(
                        errors.miesto ??
                        'Miesto sa nepodarilo odstrániť.'
                    );
                },
            });
        }
    }

    function upravMiesto(miesto: Miesto) {
        const adresa = window.prompt('Nová adresa:', miesto.adresa);
        if (adresa === null) {
            return;
        }

        const typ = window.prompt('Nový typ:', miesto.typ ?? '');
        if (typ === null) {
            return;
        }

        const popis= window.prompt('Nový popis:', miesto.popis ?? '');
        if (popis === null) {
            return;
        }

        router.put(`/moderator/miesta/${miesto.id}`, {
            adresa,
            typ,
            popis,
        });
    }

    function schvalKategoriu(id: number) {
        router.patch(`/moderator/kategorie/${id}/schvalit`);
    }

    function zamietniKategoriu(id: number) {
        router.patch(`/moderator/kategorie/${id}/zamietnut`);
    }

    function vymazKategoriu(id: number) {
        if (window.confirm('Odstrániť túto kategóriu?')) {
            router.delete(`/moderator/kategorie/${id}`, {
                onError: (errors) => {
                    window.alert(
                        errors.kategoria ??
                        'Kategóriu sa nepodarilo odstrániť.'
                    );
                },
            });
        }
    }

    function upravKategoriu(kategoria: Kategorie) {
        const typ = window.prompt('Nový názov kategórie:', kategoria.typ);

        if (typ === null) {
            return;
        }

        router.put(`/moderator/kategorie/${kategoria.id}`, {
            typ,
        });
    }

    return (
        <>
            <Head title="Moderovanie" />

            <main className="moderation-container">
                <div className="moderation-header">
                    <h1>Moderovanie</h1>
                </div>

                <section className="moderation-section">
                    <div className="moderation-section-header">
                        <h2>Čakajúce udalosti</h2>
                        <span>{udalosti.length}</span>
                    </div>

                    {udalosti.length === 0 ? (
                        <p className="moderation-empty">
                            Žiadne udalosti nečakajú na schválenie.
                        </p>
                    ) : (
                        <div className="moderation-list">
                            {udalosti.map((udalost) => (
                                <article
                                    className="moderation-card"
                                    key={udalost.id}
                                >
                                    <div className="moderation-card-content">
                                        <h3>{udalost.oznacenie}</h3>

                                        <p className="moderation-secondary">
                                            {udalost.datum_od}{' '}
                                            {udalost.cas_od}
                                            {' – '}
                                            {udalost.datum_do}{' '}
                                            {udalost.cas_do}
                                        </p>

                                        {udalost.popis && (
                                            <p>{udalost.popis}</p>
                                        )}

                                        <div className="moderation-details">
                                            <span>
                                                Miesto:{' '}
                                                <strong>
                                                    {udalost.miesto?.adresa ??
                                                        'Neuvedené'}
                                                </strong>
                                            </span>

                                            <span>
                                                Kategória:{' '}
                                                <strong>
                                                    {udalost.kategoria?.typ ??
                                                        'Neuvedená'}
                                                </strong>
                                            </span>

                                            <span>
                                                Zakladateľ:{' '}
                                                <strong>
                                                    {udalost.zakladatel?.meno ??
                                                        'Neznámy'}
                                                </strong>
                                            </span>

                                            <span>
                                                Kapacita:{' '}
                                                <strong>
                                                    {udalost.kapacita ??
                                                        'Neobmedzená'}
                                                </strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div className="moderation-actions">
                                        <button
                                            type="button"
                                            className="moderation-approve"
                                            onClick={() =>
                                                schvalUdalost(udalost.id)
                                            }
                                        >
                                            Schváliť
                                        </button>

                                        <button
                                            type="button"
                                            className="moderation-reject"
                                            onClick={() =>
                                                zamietniUdalost(udalost.id)
                                            }
                                        >
                                            Zamietnuť
                                        </button>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                <section className="moderation-section">
                    <div className="moderation-section-header">
                        <h2>Čakajúce miesta</h2>
                        <span>{miesta.length}</span>
                    </div>

                    {miesta.length === 0 ? (
                        <p className="moderation-empty">
                            Žiadne miesta nečakajú na schválenie.
                        </p>
                    ) : (
                        <div className="moderation-list">
                            {miesta.map((miesto) => (
                                <article
                                    className="moderation-card"
                                    key={miesto.id}
                                >
                                    <div className="moderation-card-content">
                                        <h3>{miesto.adresa}</h3>

                                        <p className="moderation-secondary">
                                            {miesto.typ}
                                        </p>

                                        {miesto.popis && (
                                            <p>{miesto.popis}</p>
                                        )}

                                        <div className="moderation-details">
                                            <span>
                                                Navrhol:{' '}
                                                <strong>
                                                    {miesto.navrhovatel?.meno ??
                                                        'Neznámy'}
                                                </strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div className="moderation-actions">
                                        <button
                                            type="button"
                                            className="moderation-approve"
                                            onClick={() =>
                                                schvalMiesto(miesto.id)
                                            }
                                        >
                                            Schváliť
                                        </button>

                                        <button
                                            type="button"
                                            className="moderation-reject"
                                            onClick={() =>
                                                zamietniMiesto(miesto.id)
                                            }
                                        >
                                            Zamietnuť
                                        </button>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                <section className="moderation-section">
                    <div className="moderation-section-header">
                        <h2>Schválené miesta</h2>
                        <span>{schvaleneMiesta.length}</span>
                    </div>

                    {schvaleneMiesta.length === 0 ? (
                        <p className="moderation-empty">
                            Žiadne schválené miesta.
                        </p>
                    ) : (
                        <div className="moderation-list">
                            {schvaleneMiesta.map((miesto) => (
                                <article
                                    className="moderation-card" key={miesto.id}>
                                    <div className="moderation-card-content">
                                        <h3>{miesto.adresa}</h3>

                                        <p className="moderation-secondary">
                                            {miesto.typ}
                                        </p>

                                        {miesto.popis && <p>{miesto.popis}</p>}
                                    </div>

                                    <div className="moderation-actions">
                                        <button
                                            type="button"
                                            className="moderation-approve"
                                            onClick={() => upravMiesto(miesto)}
                                        >
                                            Upraviť
                                        </button>

                                        <button
                                            type="button"
                                            className="moderation-reject"
                                            onClick={() =>
                                                 vymazMiesto(miesto.id)}
                                        >
                                            Odstraniť
                                        </button>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                <section className="moderation-section">
                    <div className="moderation-section-header">
                        <h2>Čakajúce kategórie</h2>
                        <span>{kategorie.length}</span>
                    </div>

                    {kategorie.length === 0 ? (
                        <p className="moderation-empty">
                            Žiadne kategórie nečakajú na schválenie.
                        </p>
                    ) : (
                        <div className="moderation-list">
                            {kategorie.map((kategoria) => (
                                <article
                                    className="moderation-card"
                                    key={kategoria.id}
                                >
                                    <div className="moderation-card-content">
                                        <h3>{kategoria.typ}</h3>

                                        <div className="moderation-details">
                                            <span>
                                                Rodičovská kategória:{' '}
                                                <strong>
                                                    {kategoria.rodic?.typ ??
                                                        'Žiadna'}
                                                </strong>
                                            </span>

                                            <span>
                                                Navrhol:{' '}
                                                <strong>
                                                    {kategoria.navrhovatel
                                                        ?.meno ?? 'Neznámy'}
                                                </strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div className="moderation-actions">
                                        <button
                                            type="button"
                                            className="moderation-approve"
                                            onClick={() =>
                                                schvalKategoriu(kategoria.id)
                                            }
                                        >
                                            Schváliť
                                        </button>

                                        <button
                                            type="button"
                                            className="moderation-reject"
                                            onClick={() =>
                                                zamietniKategoriu(kategoria.id)
                                            }
                                        >
                                            Zamietnuť
                                        </button>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                <section className="moderation-section">
                    <div className="moderation-section-header">
                        <h2>Schválené kategórie</h2>
                        <span>{schvaleneKategorie.length}</span>
                    </div>

                    {schvaleneKategorie.length === 0 ? (
                        <p className="moderation-empty">
                            Žiadne schválené kategórie.
                        </p>
                    ) : (
                        <div className="moderation-list">
                            {schvaleneKategorie.map((kategoria) => (
                                <article
                                    className="moderation-card" key={kategoria.id}>
                                    <div className="moderation-card-content">
                                        <h3>{kategoria.typ}</h3>


                                        <div className="moderation-details">
                                            <span>
                                                Rodičovská kategória:{' '}
                                                <strong>
                                                    {kategoria.rodic?.typ ?? 'Žiadna'}
                                                </strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div className="moderation-actions">
                                     <button
                                        type="button"
                                        className="moderation-approve"
                                        onClick={() => upravKategoriu(kategoria)}
                                    >
                                        Upraviť
                                    </button>

                                    <button 
                                        type="button"
                                        className="moderation-reject"
                                        onClick={() => vymazKategoriu(kategoria.id)}
                                    >
                                        Odstrániť
                                    </button>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </section>

            </main>
        </>
    );
}