import {Head, Link} from '@inertiajs/react';
import '../../../css/miesto-detail.css';

type Foto = {
    id: number;
    url: string;
}

type Udalost = {
    id: number;
    oznacenie: string;
    datum_od: string;
    cas_od: string;
    datum_do: string;
    cas_do: string;
    popis: string | null;
};

type Miesto = {
    id: number;
    adresa: string;
    typ: string | null;
    popis: string | null;
    foto: Foto[];
    udalosti: Udalost[];
};

type Props = {
    miesto: Miesto;
};

export default function Show({miesto}: Props) {
    return (
        <>
            <Head title={miesto.adresa} />

            <main className="miesto-container">
                <Link href="/udalosti">
                    Späť na udalosti
                </Link>

                <h1>{miesto.adresa}</h1>

                {miesto.typ && (
                    <p>{miesto.typ}</p>
                )}

                {miesto.popis && (
                    <section>
                        <h2>O mieste</h2>
                        <p>{miesto.popis}</p>
                    </section>
                )}

                {miesto.udalosti.length > 0 && (
                    <section className="miesto-udalosti">
                        <h2>Udalosti na tomto mieste</h2>

                        <div className="miesto-udalosti-list">
                            {miesto.udalosti.map((udalost) => (
                                <Link
                                    key={udalost.id}
                                    href={`/udalosti/${udalost.id}`}
                                    className="miesto-udalost"
                                >
                                    <strong>{udalost.oznacenie}</strong>

                                    <span>
                                        {udalost.datum_od} {udalost.cas_od}
                                    </span>
                                </Link>
                            ))}
                        </div>
                    </section>
                )}

                {miesto.foto.length > 0 && (
                    <section>
                        <h2>Fotogaléria</h2>

                        <div className="miesto-galeria">
                            {miesto.foto.map((foto) => (
                                <img
                                    key={foto.id}
                                    src={foto.url}
                                    alt={miesto.adresa}
                                />
                            ))}
                        </div>
                    </section>
                )}
            </main>
        </>
    );
}