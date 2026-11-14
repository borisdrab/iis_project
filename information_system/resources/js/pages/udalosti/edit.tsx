import { Head, Link, useForm } from '@inertiajs/react';
import type { SyntheticEvent } from 'react';
import '../../../css/udalost-form.css';

type Miesto = {
    id: number;
    adresa: string;
};

type Kategorie = {
    id: number;
    typ: string;
    rodic_id: number | null;
};

type Vstupne = {
    typ: string;
    cennik: string;
};

type Udalost = {
    id: number;
    oznacenie: string;
    datum_od: string;
    cas_od: string;
    datum_do: string;
    cas_do: string;
    vedenie_sedenia: string | null;
    kapacita: number | null;
    popis: string | null;
    ikona: string | null;
    miesto_id: number;
    kategoria_id: number;
};

type Props = {
    udalost: Udalost;
    vstupne: Vstupne[];
    miesta: Miesto[];
    kategorie: Kategorie[];
};

export default function Edit({
    udalost,
    vstupne,
    miesta,
    kategorie,
}: Props) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        oznacenie: udalost.oznacenie,
        datum_od: udalost.datum_od,
        cas_od: udalost.cas_od,
        datum_do: udalost.datum_do,
        cas_do: udalost.cas_do,
        vedenie_sedenia: udalost.vedenie_sedenia ?? '',
        kapacita: udalost.kapacita?.toString() ?? '',
        popis: udalost.popis ?? '',
        ikona: udalost.ikona ?? '',
        miesto_id: udalost.miesto_id.toString(),
        kategoria_id: udalost.kategoria_id.toString(),
        vstupne: vstupne.map((vstup) => ({
            typ: vstup.typ,
            cennik: vstup.cennik,
        })),
    });

    function submit(
        event: SyntheticEvent<HTMLFormElement>
    ) {
        event.preventDefault();

        put('/udalosti/${udalost.id}');
    }

    function pridajVstupne() {
        setData('vstupne', [
            ...data.vstupne,
            {
                typ: '',
                cennik: '',
            },
        ]);
    }

    function odstranVstupne(index: number) {
        setData(
            'vstupne',
            data.vstupne.filter(
                (_, currentIndex) => currentIndex !== index
            )
        );
    }

    function zmenVstupne(
        index: number,
        field: keyof Vstupne,
        value: string
    ) {
        const noveVstupne = [...data.vstupne];

        noveVstupne[index] = {
            ...noveVstupne[index],
            [field]: value,
        };

        setData('vstupne', noveVstupne);
    }

    function renderCategoryOptions(
        parentId: number | null,
        level = 0
    ): React.ReactNode[] {
        return kategorie
            .filter(
                (kategoria) =>
                    kategoria.rodic_id === parentId
            )
            .flatMap((kategoria) => [
                <option
                    key={kategoria.id}
                    value={kategoria.id}
                >
                    {'— '.repeat(level)}
                    {kategoria.typ}
                </option>,

                ...renderCategoryOptions(
                    kategoria.id,
                    level + 1
                ),
            ]);
    }

    return (
        <>
            <Head title="Upraviť udalosť" />

            <main className="udalost-form-container">
                <div className="udalost-form-header">
                    <div>
                        <Link
                            href={`/udalosti/${udalost.id}`}
                            className="udalost-form-back"
                        >
                            ← Späť na detail udalosti
                        </Link>

                        <h1>Upraviť udalosť</h1>

                        <p>
                            Upravte informácie o udalosti.
                        </p>
                    </div>
                </div>

                <form
                    className="udalost-form"
                    onSubmit={submit}
                >
                    <section className="udalost-form-section">
                        <h2>Základné informácie</h2>

                        <div className="form-field">
                            <label htmlFor="oznacenie">
                                Označenie udalosti
                            </label>

                            <input
                                id="oznacenie"
                                type="text"
                                value={data.oznacenie}
                                onChange={(event) =>
                                    setData(
                                        'oznacenie',
                                        event.target.value
                                    )
                                }
                                placeholder="Napr. FIT-AI-2026"
                                required
                            />

                            {errors.oznacenie && (
                                <p className="form-error">
                                    {errors.oznacenie}
                                </p>
                            )}
                        </div>

                        <div className="form-field">
                            <label htmlFor="popis">
                                Popis
                            </label>

                            <textarea
                                id="popis"
                                rows={5}
                                value={data.popis}
                                onChange={(event) =>
                                    setData(
                                        'popis',
                                        event.target.value
                                    )
                                }
                                placeholder="Popis udalosti..."
                            />

                            {errors.popis && (
                                <p className="form-error">
                                    {errors.popis}
                                </p>
                            )}
                        </div>
                    </section>

                    <section className="udalost-form-section">
                        <h2>Dátum a čas</h2>

                        <div className="form-grid">
                            <div className="form-field">
                                <label htmlFor="datum_od">
                                    Dátum začiatku
                                </label>

                                <input
                                    id="datum_od"
                                    type="date"
                                    value={data.datum_od}
                                    onChange={(event) =>
                                        setData(
                                            'datum_od',
                                            event.target.value
                                        )
                                    }
                                    required
                                />

                                {errors.datum_od && (
                                    <p className="form-error">
                                        {errors.datum_od}
                                    </p>
                                )}
                            </div>

                            <div className="form-field">
                                <label htmlFor="cas_od">
                                    Čas začiatku
                                </label>

                                <input
                                    id="cas_od"
                                    type="time"
                                    value={data.cas_od}
                                    onChange={(event) =>
                                        setData(
                                            'cas_od',
                                            event.target.value
                                        )
                                    }
                                    required
                                />

                                {errors.cas_od && (
                                    <p className="form-error">
                                        {errors.cas_od}
                                    </p>
                                )}
                            </div>

                            <div className="form-field">
                                <label htmlFor="datum_do">
                                    Dátum konca
                                </label>

                                <input
                                    id="datum_do"
                                    type="date"
                                    min={data.datum_od}
                                    value={data.datum_do}
                                    onChange={(event) =>
                                        setData(
                                            'datum_do',
                                            event.target.value
                                        )
                                    }
                                    required
                                />

                                {errors.datum_do && (
                                    <p className="form-error">
                                        {errors.datum_do}
                                    </p>
                                )}
                            </div>

                            <div className="form-field">
                                <label htmlFor="cas_do">
                                    Čas konca
                                </label>

                                <input
                                    id="cas_do"
                                    type="time"
                                    value={data.cas_do}
                                    onChange={(event) =>
                                        setData(
                                            'cas_do',
                                            event.target.value
                                        )
                                    }
                                    required
                                />

                                {errors.cas_do && (
                                    <p className="form-error">
                                        {errors.cas_do}
                                    </p>
                                )}
                            </div>
                        </div>
                    </section>

                    <section className="udalost-form-section">
                        <h2>Miesto a kategória</h2>

                        <div className="form-grid">
                            <div className="form-field">
                                <label htmlFor="miesto_id">
                                    Miesto
                                </label>

                                <select
                                    id="miesto_id"
                                    value={data.miesto_id}
                                    onChange={(event) =>
                                        setData(
                                            'miesto_id',
                                            event.target.value
                                        )
                                    }
                                    required
                                >
                                    <option value="">
                                        Vyber miesto
                                    </option>

                                    {miesta.map((miesto) => (
                                        <option
                                            key={miesto.id}
                                            value={miesto.id}
                                        >
                                            {miesto.adresa}
                                        </option>
                                    ))}
                                </select>

                                {errors.miesto_id && (
                                    <p className="form-error">
                                        {errors.miesto_id}
                                    </p>
                                )}
                            </div>

                            <div className="form-field">
                                <label htmlFor="kategoria_id">
                                    Kategória
                                </label>

                                <select
                                    id="kategoria_id"
                                    value={data.kategoria_id}
                                    onChange={(event) =>
                                        setData(
                                            'kategoria_id',
                                            event.target.value
                                        )
                                    }
                                    required
                                >
                                    <option value="">
                                        Vyber kategóriu
                                    </option>

                                    {renderCategoryOptions(null)}
                                </select>

                                {errors.kategoria_id && (
                                    <p className="form-error">
                                        {errors.kategoria_id}
                                    </p>
                                )}
                            </div>
                        </div>
                    </section>

                    <section className="udalost-form-section">
                        <h2>Kapacita a ďalšie údaje</h2>

                        <div className="form-grid">
                            <div className="form-field">
                                <label htmlFor="kapacita">
                                    Kapacita
                                </label>

                                <input
                                    id="kapacita"
                                    type="number"
                                    min="1"
                                    value={data.kapacita}
                                    onChange={(event) =>
                                        setData(
                                            'kapacita',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Prázdne = neobmedzená"
                                />

                                <span className="form-help">
                                    Ak pole necháš prázdne,
                                    kapacita bude neobmedzená.
                                </span>

                                {errors.kapacita && (
                                    <p className="form-error">
                                        {errors.kapacita}
                                    </p>
                                )}
                            </div>

                            <div className="form-field">
                                <label htmlFor="vedenie_sedenia">
                                    Vedenie sedenia
                                </label>

                                <input
                                    id="vedenie_sedenia"
                                    type="text"
                                    value={data.vedenie_sedenia}
                                    onChange={(event) =>
                                        setData(
                                            'vedenie_sedenia',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Nepovinné"
                                />

                                {errors.vedenie_sedenia && (
                                    <p className="form-error">
                                        {errors.vedenie_sedenia}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="form-field">
                            <label htmlFor="ikona">
                                Ikona
                            </label>

                            <input
                                id="ikona"
                                type="text"
                                value={data.ikona}
                                onChange={(event) =>
                                    setData(
                                        'ikona',
                                        event.target.value
                                    )
                                }
                                placeholder="URL alebo cesta k ikone"
                            />

                            {errors.ikona && (
                                <p className="form-error">
                                    {errors.ikona}
                                </p>
                            )}
                        </div>
                    </section>

                    <section className="udalost-form-section">
                        <div className="vstupne-header">
                            <div>
                                <h2>Vstupné</h2>
                            </div>

                            <button
                                type="button"
                                className="secondary-button"
                                onClick={pridajVstupne}
                            >
                                + Pridať vstupné
                            </button>
                        </div>

                        {data.vstupne.length === 0 ? (
                            <p className="vstupne-empty">
                                Zatiaľ nebol pridaný žiadny typ
                                vstupného.
                            </p>
                        ) : (
                            <div className="vstupne-list">
                                {data.vstupne.map(
                                    (vstup, index) => (
                                        <div
                                            className="vstupne-row"
                                            key={index}
                                        >
                                            <div className="form-field">
                                                <label>
                                                    Typ vstupného
                                                </label>

                                                <input
                                                    type="text"
                                                    value={
                                                        vstup.typ
                                                    }
                                                    onChange={(
                                                        event
                                                    ) =>
                                                        zmenVstupne(
                                                            index,
                                                            'typ',
                                                            event
                                                                .target
                                                                .value
                                                        )
                                                    }
                                                    placeholder="Študent"
                                                    required
                                                />

                                                {errors[
                                                    `vstupne.${index}.typ`
                                                ] && (
                                                    <p className="form-error">
                                                        {
                                                            errors[
                                                                `vstupne.${index}.typ`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>

                                            <div className="form-field">
                                                <label>
                                                    Cena (€)
                                                </label>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    value={
                                                        vstup.cennik
                                                    }
                                                    onChange={(
                                                        event
                                                    ) =>
                                                        zmenVstupne(
                                                            index,
                                                            'cennik',
                                                            event
                                                                .target
                                                                .value
                                                        )
                                                    }
                                                    placeholder="0.00"
                                                    required
                                                />

                                                {errors[
                                                    `vstupne.${index}.cennik`
                                                ] && (
                                                    <p className="form-error">
                                                        {
                                                            errors[
                                                                `vstupne.${index}.cennik`
                                                            ]
                                                        }
                                                    </p>
                                                )}
                                            </div>

                                            <button
                                                type="button"
                                                className="remove-button"
                                                onClick={() =>
                                                    odstranVstupne(
                                                        index
                                                    )
                                                }
                                            >
                                                Odstrániť
                                            </button>
                                        </div>
                                    )
                                )}
                            </div>
                        )}
                    </section>

                    <div className="udalost-form-actions">
                        <Link
                            href={`/udalosti/${udalost.id}`}
                            className="cancel-button"
                        >
                            Zrušiť
                        </Link>

                        <button
                            type="submit"
                            className="submit-button"
                            disabled={processing}
                        >
                            {processing
                                ? 'Ukladá sa...'
                                : 'Uložiť zmeny'}
                        </button>
                    </div>
                </form>
            </main>
        </>
    );
}