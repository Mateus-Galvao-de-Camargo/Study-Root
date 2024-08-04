<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Assunto extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('assunto');
        $table  ->addColumn('id_assunto', 'integer', ['limit' => 13])
                ->addColumn('titulo', 'string', ['limit' => 255])
                ->addColumn('resumo', 'string', ['limit' => 255])
                ->addColumn('senha', 'string', ['limit' => 255])
                ->addColumn('id_estudante_fk', 'integer', ['limit' => 11])
                ->addForeignKey('id_estudante_fk', 'estudante', 'id_estudante', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
                ->create();
    }
}
