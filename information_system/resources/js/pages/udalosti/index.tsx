import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import type { SyntheticEvent } from 'react';
import '../../../css/udalosti.css'

type Miesto = {
    id: number;
    adresa: string;
};

type Kategorie = {
    id: number;
    typ: string;
    rodic_id: number | null;
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
    miesto: Miesto;
    kategoria: Kategorie;
};

type Filters = {
    search: string;
    kategoria: string | null;
}

type Props = {
    udalosti: Udalost[];
    kategorie: Kategorie[];
    filters: Filters;
};

export default function Index({ udalosti, kategorie, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [kategoria, setKategoria] = useState(filters.kategoria?.toString() ?? '');

    function handleSubmit(event: SyntheticEvent<HTMLFormElement>) {
        event.preventDefault();

        const params: Record<string, string> = {};

        if (search.trim() !== '') {
            params.search = search.trim();
        }

        if (kategoria !== '') {
            params.kategoria = kategoria;
        }

        router.get('/', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }

    function handleReset() {
        setSearch('');
        setKategoria('');

        router.get('/');
    }

    function renderCategoryOptions(
        parentId: number | null,
        level = 0
    ): React.ReactNode[] {
        return kategorie
            .filter((category) => category.rodic_id === parentId)
            .flatMap((category) => [
                <option key={category.id} value={category.id}>
                    {'— '.repeat(level)}
                    {category.typ}
                </option>,

                ...renderCategoryOptions(category.id, level + 1),
            ]);
    }

    return (
        <>
            <Head title="Udalosti" />
            <main className="udalosti-container">
                <h1 className="udalosti-heading">Udalosti</h1>

                <form
                    className="udalosti-filter"
                    onSubmit={handleSubmit}
                >
                    <div className="filter-field">
                        <label htmlFor="search">
                            Vyhľadávanie
                        </label>

                        <input
                            id="search"
                            type="text"
                            value={search}
                            onChange={(event) =>
                                setSearch(event.target.value)
                            }
                            placeholder="Hľadať udalosť..."
                        />
                    </div>

                    <div className="filter-field">
                        <label htmlFor="kategoria">
                            Kategória
                        </label>

                        <select
                            id="kategoria"
                            value={kategoria}
                            onChange={(event) =>
                                setKategoria(event.target.value)
                            }
                        >
                            <option value="">
                                Všetky kategórie
                            </option>

                            {renderCategoryOptions(null)}
                        </select>
                    </div>

                    <div className="filter-buttons">
                        <button type="submit">
                            Filtrovať
                        </button>

                        <button
                            type="button"
                            onClick={handleReset}
                        >
                            Zrušiť filtre
                        </button>
                    </div>
                </form>

                <p className="udalosti-pocet">
                    Nájdených udalostí: {udalosti.length}
                </p>

                {udalosti.length === 0 ? (
                    <p className="udalosti-empty">
                        Nenašli sa žiadne udalosti.
                    </p>
                ) : (
                    <div className="udalosti-grid">
                        {
                            udalosti.map((udalost) => (
                                <article className="udalosti-card" key={udalost.id}>
                                    <p className="udalosti-kategoria">
                                        {udalost.kategoria?.typ ?? 'Bez kategorie'}
                                    </p>

                                    <h2 className="udalosti-oznacenie">
                                        {udalost.oznacenie}
                                    </h2>

                                    <p className="udalosti-datum">
                                        {udalost.datum_od}{' '}
                                        {udalost.cas_od}
                                    </p>

                                    <p className="udalosti-miesto">
                                    {udalost.miesto?.adresa ?? 'Miesto neuvedené'}
                                    </p>

                                    {udalost.popis && (
                                        <p className="udalosti-popis">
                                            {udalost.popis}
                                        </p>
                                    )}

                                    <p className="udalosti-kapacita">
                                    Kapacita:{' '}
                                    {udalost.kapacita === null
                                        ? 'neobmedzená'
                                        : udalost.kapacita}
                                    </p>

                                    <Link href={`/udalosti/${udalost.id}`} className="udalosti-detail-link">
                                        Zobraziť detail
                                    </Link>
                                </article>
                            )
                    )}
                    </div>
                )}
            </main>
        </>
    );
}