import { Head, Link, useForm, usePage } from '@inertiajs/react';
import '../../../css/udalosti-detail.css';
import type { SyntheticEvent } from 'react';

type Miesto = {
    id: number;
    adresa: string;
    typ: string | null;
    popis: string | null;
};

type Kategorie = {
    id: number;
    typ: string;
};

type Vstupne = {
    id: number;
    cennik: string;
    typ: string;
}

type Foto = {
    id: number;
    url: string;
}

type Osoba = {
    id: number;
    meno: string;
}

type Uzivatel = {
    id: number;
    osoba: Osoba | null;
}

type Komentar = {
    id: number;
    text: string | null;
    hodnotenie: number | null;
    uzivatel: Uzivatel | null;
}

type Udalost = {
    id: number;
    oznacenie: string;
    datum_od: string;
    cas_od: string;
    datum_do: string;
    cas_do: string;
    kapacita: number | null;
    popis: string | null;
    ikona: string | null;
    miesto: Miesto | null;
    kategoria: Kategorie | null;
    vstupne: Vstupne[];
    foto: Foto[];
    komentare: Komentar[];
    zakladatel_id: number;
};

type Props = {
    udalost: Udalost;
    hodnotenie: number | null;
    pocetHodnoteni: number;
};

export default function Show({ udalost, hodnotenie, pocetHodnoteni }: Props) {
    const { auth } = usePage<{
        auth: {
            user: {
                id: number;
                meno: string;
            } | null;
        };
    }>().props;

    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
    } = useForm({
        text: '',
        hodnotenie: '',
    });

    const {
        data: fotoData,
        setData: setFotoData,
        post: postFoto,
        processing: fotoProcessing,
        errors: fotoErrors,
        reset: resetFoto,
    } = useForm<{
        foto: File | null;
    }>({
        foto: null,
    });

    function submitKomentar(
        event: SyntheticEvent<HTMLFormElement>
    ) {
        event.preventDefault();

        post(`/udalosti/${udalost.id}/komentare`, {
            preserveScroll: true,

            onSuccess: () => {
                reset();
            },
        });
    }

    function submitFoto(event: SyntheticEvent<HTMLFormElement>) {
        event.preventDefault();

        postFoto(`/udalosti/${udalost.id}/foto`, {
            forceFormData: true,
            preserveScroll: true,

            onSuccess: () => {
                resetFoto();
            }
        });
    }

    return (
        <>
            <Head title={udalost.oznacenie} />

            <main className="udalost-container">
                <Link
                    href="/udalosti"
                    className="udalost-back-link"
                >
                    ← Späť na udalosti
                </Link>

                <h1 className="udalost-oznacenie">
                    {udalost.oznacenie}
                </h1>

                {auth.user?.id === udalost.zakladatel_id && (
                    <Link
                        href={`/udalosti/${udalost.id}/edit`}
                        className="udalost-edit-button"
                    >
                        Upraviť udalosť
                    </Link>
                )}

                <p className="udalost-kategoria">
                    {udalost.kategoria?.typ ?? 'Bez kategórie'}
                </p>

                <section className="udalost-info">
                    <div>
                        <strong>Začiatok:</strong>{' '}
                        {udalost.datum_od} {udalost.cas_od}
                    </div>

                    <div>
                        <strong>Koniec:</strong>{' '}
                        {udalost.datum_do} {udalost.cas_do}
                    </div>

                    <div>
                        <strong>Miesto:</strong>{' '}
                        {udalost.miesto ? (
                            <Link href={`/miesta/${udalost.miesto.id}`}>
                                {udalost.miesto.adresa}
                            </Link>
                        ) : (
                            'Neuvedené'
                        )}
                    </div>

                    <div>
                        <strong>Kapacita:</strong>{' '}
                        {udalost.kapacita === null
                            ? 'Neobmedzená'
                            : udalost.kapacita}
                    </div>
                </section>

                {udalost.popis && (
                    <section className="udalost-popis">
                        <h2>Popis</h2>
                        <p>{udalost.popis}</p>
                    </section>
                )}

                {udalost.vstupne.length > 0 && (
                    <section className="udalost-vstupne">
                        <h2>Vstupné</h2>

                        <div className="vstupne-list">
                            {udalost.vstupne.map((vstup) => (
                                <div
                                    className="vstupne-item"
                                    key={vstup.id}
                                >
                                    <span>{vstup.typ}</span>

                                    <strong>
                                        {Number(vstup.cennik) === 0
                                            ? 'Zdarma'
                                            : `${Number(vstup.cennik).toFixed(2)} €`}
                                    </strong>
                                </div>
                            ))}
                        </div>
                    </section>
                )}

                {auth.user && (
                    <div className="udalost-actions">
                        <button type="button">
                            Prihlásiť sa na udalosť
                        </button>
                    </div>
                )}

                {auth.user?.id === udalost.zakladatel_id && (
                    <section className="udalost-upload">
                        <h2>Pridať fotografiu</h2>

                        <form onSubmit={submitFoto}>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                onChange={(event) =>
                                    setFotoData(
                                        'foto',
                                        event.target.files?.[0] ?? null
                                    )
                                }
                            />

                            {fotoErrors.foto && (
                                <p className="foto-error">
                                    {fotoErrors.foto}
                                </p>
                            )}

                            <button
                                type="submit"
                                disabled={
                                    fotoProcessing || fotoData.foto === null
                                }
                            >
                                {fotoProcessing
                                    ? 'Nahrávam...'
                                    : 'Nahrať fotografiu'}
                            </button>
                        </form>
                    </section>
                )}

                {udalost.foto.length > 0 && (
                    <section className="udalost-galeria">
                        <h2>Fotogaléria</h2>

                        <div className="galeria-grid">
                            {udalost.foto.map((foto) => (
                                <img
                                    key={foto.id}
                                    src={foto.url}
                                    alt={udalost.oznacenie}
                                    className="galeria-foto"
                                />
                            ))}
                        </div>
                    </section>
                )}

                <section className="udalost-komentare">
                    <h2>Komentáre a hodnotenia</h2>

                    {hodnotenie !== null ? (
                        <div className="priemerne-hodnotenie">
                            <span className="priemer-hviezdy">
                                {'★'.repeat(Math.round(hodnotenie))}
                                {'☆'.repeat(5 - Math.round(hodnotenie))}
                            </span>

                            <strong>
                                {Number(hodnotenie).toFixed(1)} / 5
                            </strong>

                            <span>
                                ({pocetHodnoteni} hodnotení)
                            </span>
                        </div>
                    ) : (
                        <p className="bez-hodnotenia">
                            Udalosť zatiaľ nemá žiadne hodnotenie.
                        </p>
                    )}

                    {udalost.komentare.length === 0 ? (
                        <p className="komentare-empty">
                            Neboli pridané žiadne komentáre.
                        </p>
                    ) : (
                        <div className="komentare-list">
                            {udalost.komentare.map((komentar) => (
                                <article
                                    className="komentar"
                                    key={komentar.id}
                                >
                                    <div className="komentar-header">
                                        <strong>
                                            {komentar.uzivatel?.osoba?.meno ??
                                                'Používateľ'}
                                        </strong>

                                        {komentar.hodnotenie !== null && (
                                            <span className="komentar-rating">
                                                {'★'.repeat(
                                                    komentar.hodnotenie
                                                )}

                                                {'☆'.repeat(
                                                    5 - komentar.hodnotenie
                                                )}
                                            </span>
                                        )}
                                    </div>

                                    {komentar.text && (
                                        <p>{komentar.text}</p>
                                    )}
                                </article>
                            ))}
                        </div>
                    )}

                    {auth.user && (
                        <form
                            className="komentar-form"
                            onSubmit={submitKomentar}
                        >
                            <h3>Pridať komentár alebo hodnotenie</h3>

                            <label htmlFor="hodnotenie">
                                Hodnotenie
                            </label>

                            <select
                                id="hodnotenie"
                                value={data.hodnotenie}
                                onChange={(event) =>
                                    setData(
                                        'hodnotenie',
                                        event.target.value
                                    )
                                }
                            >
                                <option value="">
                                    Bez hodnotenia
                                </option>

                                <option value="5">
                                    5 - Výborné
                                </option>

                                <option value="4">
                                    4 - Dobré
                                </option>

                                <option value="3">
                                    3 - Neutrálne
                                </option>

                                <option value="2">
                                    2 - Zlé
                                </option>

                                <option value="1">
                                    1 - Hrozné
                                </option>
                            </select>

                            <label htmlFor="komentar">
                                Komentár
                            </label>

                            <textarea
                                id="komentar"
                                rows={5}
                                value={data.text}
                                onChange={(event) =>
                                    setData('text', event.target.value)
                                }
                                placeholder="Napíš svoj názor na udalosť..."
                            />

                            {errors.text && (
                                <p className="komentar-error">
                                    {errors.text}
                                </p>
                            )}

                            {errors.hodnotenie && (
                                <p className="komentar-error">
                                    {errors.hodnotenie}
                                </p>
                            )}

                            <button
                                type="submit"
                                disabled={processing}
                            >
                                {processing
                                    ? 'Odosielam...'
                                    : 'Pridať'}
                            </button>
                        </form>
                    )}
                </section>
            </main>
        </>
    );
}