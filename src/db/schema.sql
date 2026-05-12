-- Schema unificado para PostgreSQL (Neon, Render Postgres, Postgres local).
-- Idempotente: pode rodar várias vezes sem efeitos colaterais.

CREATE TABLE IF NOT EXISTS estudante (
    id_estudante SERIAL PRIMARY KEY,
    usuario      VARCHAR(30)  NOT NULL,
    email        VARCHAR(120) NOT NULL UNIQUE,
    senha        VARCHAR(255) NOT NULL,
    created_at   TIMESTAMPTZ  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assunto (
    id_assunto      SERIAL PRIMARY KEY,
    titulo          VARCHAR(60)  NOT NULL,
    resumo          VARCHAR(300),
    id_estudante_fk INTEGER      NOT NULL REFERENCES estudante(id_estudante) ON DELETE CASCADE,
    created_at      TIMESTAMPTZ  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS anotacao (
    id_anotacao    SERIAL PRIMARY KEY,
    titulo         VARCHAR(60) NOT NULL,
    conteudo       TEXT,
    id_assunto_fk  INTEGER     NOT NULL REFERENCES assunto(id_assunto) ON DELETE CASCADE,
    created_at     TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_assunto_estudante  ON assunto(id_estudante_fk);
CREATE INDEX IF NOT EXISTS idx_anotacao_assunto   ON anotacao(id_assunto_fk);
CREATE UNIQUE INDEX IF NOT EXISTS uq_assunto_titulo_por_estudante
    ON assunto(id_estudante_fk, titulo);
CREATE UNIQUE INDEX IF NOT EXISTS uq_anotacao_titulo_por_assunto
    ON anotacao(id_assunto_fk, titulo);
