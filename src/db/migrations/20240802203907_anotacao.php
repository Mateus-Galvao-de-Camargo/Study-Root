<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Anotacao extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('anotacao');
        $table  ->addColumn('id_anotacao', 'integer', ['limit' => 15])
                ->addColumn('titulo', 'string', ['limit' => 255])
                ->addColumn('conteudo', 'text')
                ->addColumn('id_assunto_fk', 'integer', ['limit' => 13])
                ->addForeignKey('id_assunto_fk', 'assunto', 'id_assunto', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                ->create();
    }
}
