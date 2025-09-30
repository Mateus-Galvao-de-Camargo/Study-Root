-- Script SQL para PostgreSQL
-- Converte o banco MySQL para PostgreSQL

CREATE TABLE IF NOT EXISTS estudante(
  id_estudante SERIAL PRIMARY KEY,
  usuario VARCHAR(30) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  senha VARCHAR(60) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS assunto(
  id_assunto SERIAL PRIMARY KEY,
  titulo VARCHAR(24) NOT NULL,
  resumo VARCHAR(300),
  id_estudante_fk INTEGER NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_estudante_fk) REFERENCES estudante(id_estudante) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS anotacao(
  id_anotacao SERIAL PRIMARY KEY,
  titulo VARCHAR(24) NOT NULL,
  conteudo TEXT,
  id_assunto_fk INTEGER NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_assunto_fk) REFERENCES assunto(id_assunto) ON DELETE CASCADE
);

-- Índices para melhor performance
CREATE INDEX IF NOT EXISTS idx_estudante_email ON estudante(email);
CREATE INDEX IF NOT EXISTS idx_assunto_estudante_fk ON assunto(id_estudante_fk);
CREATE INDEX IF NOT EXISTS idx_anotacao_assunto_fk ON anotacao(id_assunto_fk);
