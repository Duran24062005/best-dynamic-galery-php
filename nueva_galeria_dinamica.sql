-- PostgreSQL. Ejecutar dentro de la base definida por DATABASE_URL.
CREATE TABLE IF NOT EXISTS fotos (
    id BIGSERIAL PRIMARY KEY,
    titulo VARCHAR(180) NOT NULL,
    imagen VARCHAR(2048) NOT NULL UNIQUE,
    text TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Las imagenes locales se almacenan en public/img/.
-- La columna imagen guarda el nombre relativo del archivo; la aplicacion
-- resuelve la ruta publica como public/img/<nombre>.
INSERT INTO fotos (titulo, imagen, text) VALUES
    ('10', '10.png', 'Imagen local heredada: public/img/10.png'),
    ('13', '13.png', 'Imagen local heredada: public/img/13.png')
ON CONFLICT (imagen) DO NOTHING;
