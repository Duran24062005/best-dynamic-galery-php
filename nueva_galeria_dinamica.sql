-- PostgreSQL. Ejecutar dentro de la base definida por DATABASE_URL.
CREATE TABLE IF NOT EXISTS fotos (
 id BIGSERIAL PRIMARY KEY,
 titulo VARCHAR(180) NOT NULL,
 imagen VARCHAR(2048) NOT NULL UNIQUE,
 text TEXT NOT NULL,
 created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);
-- Los registros heredados pueden conservar nombres locales; los nuevos usan URLs de Vercel Blob.
INSERT INTO fotos (titulo, imagen, text) VALUES
('10', '10.png', 'Imagen local heredada: 10.png'),
('13', '13.png', 'Imagen local heredada: 13.png')
ON CONFLICT (imagen) DO NOTHING;
